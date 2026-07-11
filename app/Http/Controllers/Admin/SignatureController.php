<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\IssuedDocument;
use App\Models\User;
use App\Services\DocumentSigningService;
use App\Services\RequestStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SignatureController extends Controller
{
    public function __construct(
        private DocumentSigningService $signingService,
        private RequestStatusService $statusService
    ) {}

    public function uploadSignature(Request $request)
    {
        $request->validate([
            'signature_data' => 'required|string|starts_with:data:image/png;base64,',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        $currentUser = Auth::user();

        if ($request->filled('user_id')) {
            if (!in_array($currentUser->role, ['admin', 'super_admin'])) {
                abort(403, 'غير مصرح لك بتعديل توقيع هذا المستخدم');
            }
            $user = User::findOrFail($request->user_id);
        } else {
            $user = $currentUser;
        }

        if ($user->signature_image) {
            Storage::disk('public')->delete($user->signature_image);
        }

        $data = explode(',', $request->signature_data)[1];
        $decoded = base64_decode($data);

        $filename = 'signatures/' . $user->id . '_' . time() . '.png';
        Storage::disk('public')->put($filename, $decoded);

        $user->update(['signature_image' => $filename]);

        return back()->with('success', 'تم حفظ التوقيع الإلكتروني بنجاح.');
    }

    public function signDocument(Request $request, IssuedDocument $issuedDocument)
    {
        $user = Auth::user();
        $roleTitle = $user->signer_role ?? $request->input('role_title');

        if (!$roleTitle) {
            return back()->with('error', 'لم يتم تعيين منصب توقيعي لحسابك. راجع المسؤول العام.');
        }

        try {
            $this->signingService->sign($user, $issuedDocument, $roleTitle, $request->ip());

            return back()->with('success', "تم التوقيع بنجاح بصفتك: {$roleTitle}");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function pendingSignatures(Request $request)
    {
        $pendingDocs = $this->signingService->getPendingForUser(
            Auth::user(),
            15,
            $request->get('search'),
            $request->get('document_type_id'),
            $request->get('date_from'),
            $request->get('date_to')
        );

        $types = DocumentType::whereIn('code', ['ACADEMIC_RECORD', 'GRADES_CERTIFICATE'])->get();

        return view('admin.pending-signatures', compact('pendingDocs', 'types'));
    }

    public function readySignatures(Request $request)
    {
        $readyDocs = $this->signingService->getCompletedDocuments(
            15,
            $request->get('search'),
            $request->get('document_type_id'),
            $request->get('date_from'),
            $request->get('date_to')
        );

        $types = DocumentType::whereIn('code', ['ACADEMIC_RECORD', 'GRADES_CERTIFICATE'])->get();

        return view('admin.ready-signatures', compact('readyDocs', 'types'));
    }

    public function exportSignatures()
    {
        return (new \App\Exports\CompletedSignaturesExport())->download('التوقيعات_الجاهزة_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function approveAndIssue(IssuedDocument $issuedDocument)
    {
        if (!$issuedDocument->all_signed_at) {
            return back()->with('error', 'لا يمكن اعتماد الوثيقة قبل اكتمال جميع التوقيعات.');
        }

        $request = $issuedDocument->documentRequest;

        if ($request->status !== 'PENDING_SIGNATURES') {
            return back()->with('error', 'الوثيقة ليست في مرحلة التوقيعات.');
        }

        try {
            $this->statusService->transition(
                $request,
                'READY',
                'تم اعتماد الوثيقة بعد اكتمال جميع التوقيعات.',
                Auth::id()
            );

            return back()->with('success', 'تم اعتماد الوثيقة وإصدارها بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reissue(IssuedDocument $issuedDocument)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'غير مصرح لك بإعادة إصدار الوثائق.');
        }

        $docRequest = $issuedDocument->documentRequest;

        if (!in_array($docRequest->status, ['READY', 'ISSUED'])) {
            return back()->with('error', 'لا يمكن إعادة الإصدار إلا للوثائق الجاهزة أو المصدرة.');
        }

        try {
            $issuanceService = app(\App\Services\DocumentIssuanceService::class);
            $issuanceService->resetForReissue($issuedDocument);

            $this->statusService->transition(
                $docRequest,
                'PENDING_SIGNATURES',
                'تم إعادة الوثيقة لمرحلة التوقيعات بعد تعديل البيانات.',
                $user->id
            );

            $this->signingService->notifyCurrentSigner($issuedDocument);

            return back()->with('success', 'تمت إعادة تعيين التوقيعات وإرسال الوثيقة للتوقيع من جديد.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
