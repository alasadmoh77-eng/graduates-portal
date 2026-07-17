<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\User;
use App\Http\Requests\StoreDocumentRequest;
use App\Notifications\NewPaymentProofSubmitted;
use App\Services\TrackingCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class DocumentRequestController extends Controller
{
    protected $trackingCodeService;
    protected $studentInfoProvider;

    public function __construct(
        TrackingCodeService $trackingCodeService,
        \App\Contracts\StudentInformationProvider $studentInfoProvider
    ) {
        $this->trackingCodeService = $trackingCodeService;
        $this->studentInfoProvider = $studentInfoProvider;
    }

    public function index(Request $request)
    {
        $query = DocumentRequest::with(['documentType', 'issuedDocument'])
            ->where('user_id', Auth::id())
            ->latest();

        if ($request->filled('filter')) {
            match ($request->filter) {
                'in_progress' => $query->whereIn('status', ['SUBMITTED', 'UNDER_REVIEW', 'APPROVED']),
                'ready' => $query->whereIn('status', ['READY', 'ISSUED']),
                'rejected' => $query->where('status', 'REJECTED'),
                default => null,
            };
        }

        $requests = $query->paginate(10)->withQueryString();
        $filter = $request->get('filter', 'all');

        return view('graduate.documents.index', compact('requests', 'filter'));
    }

    public function create()
    {
        $types = DocumentType::whereIn('code', ['ACADEMIC_RECORD', 'GRADES_CERTIFICATE'])->get();
        return view('graduate.documents.create', compact('types'));
    }

    public function store(StoreDocumentRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['tracking_code'] = $this->trackingCodeService->generateNextCode();
        $data['status'] = 'SUBMITTED';

        $documentType = DocumentType::findOrFail($data['document_type_id']);

        if (in_array($documentType->code, ['ACADEMIC_RECORD', 'GRADES_CERTIFICATE'])) {
            $hasAcademicData = $this->studentInfoProvider->hasAcademicRecord(Auth::user());
            
            if (!$hasAcademicData) {
                return back()->with('error', 'لا يمكن طلب هذه الوثيقة لأن السجل الأكاديمي غير مدخل لهذا الطالب.');
            }
        }

        $data['fee_amount'] = $documentType->fee_amount;
        $data['currency'] = $documentType->currency;
        $data['payment_required'] = $documentType->payment_required;

        if ($documentType->payment_required) {
            $data['payment_status'] = 'pending_review';
            if ($request->hasFile('payment_proof')) {
                // تخزين إثبات الدفع في القرص الخاص (local) وليس العام
                // لمنع الوصول المباشر عبر URL بدون صلاحيات
                $data['payment_proof_path'] = $request->file('payment_proof')
                    ->store('payment-proofs', 'local');
            }
        } else {
            $data['payment_status'] = 'not_required';
        }

        $docRequest = DocumentRequest::create($data);

        if ($docRequest->payment_required) {
            if ($request->hasFile('payment_proof')) {
                try {
                    $financeUsers = User::where('role', 'finance_admin')
                        ->where('is_active', true)
                        ->get();

                    if ($financeUsers->isEmpty()) {
                        \Illuminate\Support\Facades\Log::warning('No active finance_admin users found to notify about new payment proof.');
                    } else {
                        $alreadyNotifiedIds = DB::table('notifications')
                            ->where('notifiable_type', User::class)
                            ->whereIn('notifiable_id', $financeUsers->pluck('id'))
                            ->where(function($q) use ($docRequest) {
                                $q->where(function($sub) use ($docRequest) {
                                    $sub->where('data->type', 'payment_proof_review')
                                        ->where('data->document_request_id', $docRequest->id);
                                })->orWhere(function($sub) use ($docRequest) {
                                    $sub->where('data->type', 'new_payment_proof_submitted')
                                        ->where('data->document_request_id', $docRequest->id);
                                });
                            })
                            ->whereNull('read_at')
                            ->pluck('notifiable_id')
                            ->toArray();

                        $usersToNotify = $financeUsers->reject(function ($u) use ($alreadyNotifiedIds) {
                            return in_array($u->id, $alreadyNotifiedIds);
                        });

                        if ($usersToNotify->isNotEmpty()) {
                            Notification::send($usersToNotify, new NewPaymentProofSubmitted($docRequest));
                        }
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to notify finance admins about new payment proof: ' . $e->getMessage());
                }
            }
        } else {
            // Free request transitions to UNDER_REVIEW immediately
            app(\App\Services\RequestStatusService::class)->moveToAcademicReview(
                $docRequest,
                'طلب مستند مجاني. انتقل مباشرة للمراجعة الأكاديمية.',
                $docRequest->user_id
            );
        }

        return redirect()->route('graduate.documents.index')
            ->with('success', __('app.document_request_submitted'));
    }

    public function show(DocumentRequest $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بمشاهدة هذا الطلب.');
        }

        $document->load([
            'documentType',
            'issuedDocument',
            'paymentReviewedBy',
            'logs' => fn ($q) => $q->reorder()->with('admin')->orderBy('created_at', 'asc'),
        ]);

        return view('graduate.documents.show', compact('document'));
    }

    public function download(DocumentRequest $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        if (!in_array($document->status, ['READY', 'ISSUED']) || !$document->issuedDocument) {
            return back()->with('error', 'المستند غير جاهز للتحميل بعد.');
        }

        $path = $document->issuedDocument->pdf_path;
        if (!Storage::disk('local')->exists($path)) {
            return back()->with('error', 'ملف المستند غير موجود على الخادم.');
        }

        return Storage::disk('local')->download($path, "Document-{$document->tracking_code}.pdf");
    }

    public function uploadPaymentProof(Request $request, DocumentRequest $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($document->payment_status, ['rejected', 'pending_upload'])) {
            return back()->with('error', 'لا يمكن رفع إثبات دفع في هذه المرحلة.');
        }

        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // حذف الملف القديم من القرص الخاص إن وجد
        if ($document->payment_proof_path && Storage::disk('local')->exists($document->payment_proof_path)) {
            Storage::disk('local')->delete($document->payment_proof_path);
        }

        // تخزين الملف الجديد في القرص الخاص (غير متاح عبر URL مباشر)
        $path = $request->file('payment_proof')->store('payment-proofs', 'local');

        $document->update([
            'payment_proof_path' => $path,
            'payment_status' => 'pending_review',
            'payment_rejection_reason' => null,
            'payment_reviewed_by' => null,
            'payment_reviewed_at' => null,
        ]);

        try {
            $financeUsers = User::where('role', 'finance_admin')
                ->where('is_active', true)
                ->get();

            if ($financeUsers->isEmpty()) {
                \Illuminate\Support\Facades\Log::warning('No active finance_admin users found to notify about payment proof re-upload.');
            } else {
                $alreadyNotifiedIds = DB::table('notifications')
                    ->where('notifiable_type', User::class)
                    ->whereIn('notifiable_id', $financeUsers->pluck('id'))
                    ->where(function($q) use ($document) {
                        $q->where(function($sub) use ($document) {
                            $sub->where('data->type', 'payment_proof_review')
                                ->where('data->document_request_id', $document->id);
                        })->orWhere(function($sub) use ($document) {
                            $sub->where('data->type', 'new_payment_proof_submitted')
                                ->where('data->document_request_id', $document->id);
                        });
                    })
                    ->whereNull('read_at')
                    ->pluck('notifiable_id')
                    ->toArray();

                $usersToNotify = $financeUsers->reject(function ($u) use ($alreadyNotifiedIds) {
                    return in_array($u->id, $alreadyNotifiedIds);
                });

                if ($usersToNotify->isNotEmpty()) {
                    Notification::send($usersToNotify, new NewPaymentProofSubmitted($document));
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to notify finance admins about payment proof re-upload: ' . $e->getMessage());
        }

        return back()->with('success', __('app.payment_proof_uploaded'));
    }

    /**
     * يتيح للخريج صاحب الطلب فقط تصفح إثبات الدفع الخاص به بأمان.
     * لا يمكن الوصول لهذا الملف بأي رابط مباشر نظراً لتخزينه في القرص الخاص.
     */
    public function viewPaymentProof(DocumentRequest $document)
    {
        // التحقق من أن الخريج هو صاحب الطلب
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$document->payment_proof_path || !Storage::disk('local')->exists($document->payment_proof_path)) {
            return back()->with('error', 'ملف إثبات الدفع غير موجود.');
        }

        $extension = strtolower(pathinfo($document->payment_proof_path, PATHINFO_EXTENSION));
        $mimeType  = match ($extension) {
            'pdf'        => 'application/pdf',
            'png'        => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            default      => 'application/octet-stream',
        };

        $fileName = 'proof-' . $document->tracking_code . '.' . $extension;

        return response()->streamDownload(function () use ($document) {
            echo Storage::disk('local')->get($document->payment_proof_path);
        }, $fileName, ['Content-Type' => $mimeType]);
    }
}
