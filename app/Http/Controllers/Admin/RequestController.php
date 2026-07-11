<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Services\RequestStatusService;
use App\Services\DocumentIssuanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    protected $statusService;
    protected $issuanceService;

    protected $studentInfoProvider;

    public function __construct(
        RequestStatusService $statusService,
        DocumentIssuanceService $issuanceService,
        \App\Contracts\StudentInformationProvider $studentInfoProvider
    ) {
        $this->statusService = $statusService;
        $this->issuanceService = $issuanceService;
        $this->studentInfoProvider = $studentInfoProvider;
    }

    /**
     * List all requests with filters
     */
    public function index(Request $request)
    {
        $query = DocumentRequest::with(['user.graduate.major', 'documentType'])->latest();

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->document_type_id);
        }
        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tracking_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requests = $query->paginate(15)->withQueryString();
        $types = DocumentType::whereIn('code', ['ACADEMIC_RECORD', 'GRADES_CERTIFICATE'])->get();

        return view('admin.requests.index', compact('requests', 'types'));
    }

    /**
     * Show request details and process
     */
    public function show(DocumentRequest $documentRequest)
    {
        $documentRequest->load([
            'user.graduate.major',
            'documentType',
            'issuedDocument',
            'logs' => fn ($q) => $q->reorder()->with('admin')->orderBy('created_at', 'asc'),
        ]);
        $availableTransitions = $this->statusService->getAvailableTransitions($documentRequest->status);

        $hasAcademicRecordData = $this->studentInfoProvider->hasAcademicRecord($documentRequest->user);

        return view('admin.requests.show', compact('documentRequest', 'availableTransitions', 'hasAcademicRecordData'));
    }

    /**
     * Update status
     */
    public function updateStatus(Request $request, DocumentRequest $documentRequest)
    {
        $request->validate([
            'status' => 'required|string|in:SUBMITTED,UNDER_REVIEW,APPROVED,PENDING_SIGNATURES,REJECTED,READY,ISSUED',
            'note' => 'nullable|string'
        ]);

        try {
            $this->statusService->transition(
                $documentRequest,
                $request->status,
                $request->note,
                Auth::id()
            );

            return redirect()->route('admin.requests.show', $documentRequest)
                ->with('success', __('app.admin_status_updated'));
        } catch (\Exception $e) {
            return back()->with('error', __('app.admin_status_error', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Generate PDF and mark as READY
     */
    public function generatePdf(DocumentRequest $documentRequest)
    {
        try {
            $this->issuanceService->issue($documentRequest, Auth::id());
            return back()->with('success', __('app.admin_pdf_generated'));
        } catch (\Exception $e) {
            return back()->with('error', __('app.admin_pdf_error', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Send document to signature workflow
     */
    public function sendForSignatures(DocumentRequest $documentRequest)
    {
        try {
            $this->issuanceService->initiateDraft($documentRequest, Auth::id());
            return back()->with('success', 'تم إرسال الوثيقة لسير التوقيعات. ستظهر في لوحة التوقيعات المعلقة للمسؤولين.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Download generated PDF from secure local storage
     */
    public function downloadPdf(DocumentRequest $documentRequest)
    {
        if (!$documentRequest->issuedDocument) {
            return back()->with('error', 'المستند غير جاهز للتحميل بعد.');
        }

        $path = $documentRequest->issuedDocument->pdf_path;
        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            return back()->with('error', 'ملف المستند غير موجود على الخادم.');
        }

        return \Illuminate\Support\Facades\Storage::disk('local')->download($path, "Document-{$documentRequest->tracking_code}.pdf");
    }
}
