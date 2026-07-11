<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Notifications\PaymentProofApproved;
use App\Notifications\PaymentProofRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentRequest::with(['user.graduate.major', 'documentType', 'paymentReviewedBy'])
            ->whereIn('payment_status', ['pending_review', 'rejected', 'approved']);

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tracking_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function showProof(DocumentRequest $documentRequest)
    {
        if (!in_array($documentRequest->payment_status, ['pending_review', 'rejected', 'approved'])) {
            abort(404);
        }

        // الملفات مخزنة في القرص الخاص (local disk) لمنع الوصول المباشر عبر URL
        if (!$documentRequest->payment_proof_path || !Storage::disk('local')->exists($documentRequest->payment_proof_path)) {
            return back()->with('error', 'ملف إثبات الدفع غير موجود.');
        }

        $extension = strtolower(pathinfo($documentRequest->payment_proof_path, PATHINFO_EXTENSION));
        $mimeType  = match ($extension) {
            'pdf'         => 'application/pdf',
            'png'         => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            default       => 'application/octet-stream',
        };

        $fileName = 'proof-' . $documentRequest->tracking_code . '.' . $extension;

        return response()->streamDownload(function () use ($documentRequest) {
            echo Storage::disk('local')->get($documentRequest->payment_proof_path);
        }, $fileName, ['Content-Type' => $mimeType]);
    }

    public function approve(DocumentRequest $documentRequest)
    {
        if ($documentRequest->payment_status !== 'pending_review') {
            return back()->with('error', 'لا يمكن اعتماد هذا الدفع.');
        }

        $documentRequest->update([
            'payment_status' => 'approved',
            'payment_reviewed_by' => Auth::id(),
            'payment_reviewed_at' => now(),
            'payment_rejection_reason' => null,
        ]);

        if ($documentRequest->status === 'SUBMITTED') {
            $documentRequest->update(['status' => 'UNDER_REVIEW']);
            \App\Models\RequestStatusLog::create([
                'document_request_id' => $documentRequest->id,
                'admin_id' => Auth::id(),
                'from_status' => 'SUBMITTED',
                'to_status' => 'UNDER_REVIEW',
                'note' => 'تم اعتماد الدفع. انتقل الطلب للمراجعة الأكاديمية.',
                'created_at' => now(),
            ]);
        }

        $documentRequest->user->notify(new PaymentProofApproved($documentRequest));

        return back()->with('success', __('app.payment_approved_success'));
    }

    public function reject(Request $request, DocumentRequest $documentRequest)
    {
        if ($documentRequest->payment_status !== 'pending_review') {
            return back()->with('error', 'لا يمكن رفض هذا الدفع.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $documentRequest->update([
            'payment_status' => 'rejected',
            'payment_reviewed_by' => Auth::id(),
            'payment_reviewed_at' => now(),
            'payment_rejection_reason' => $request->rejection_reason,
        ]);

        $documentRequest->user->notify(new PaymentProofRejected($documentRequest, $request->rejection_reason));

        return back()->with('success', __('app.payment_rejected_success'));
    }
}
