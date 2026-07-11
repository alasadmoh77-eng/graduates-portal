<?php

namespace App\Services;

use App\Models\IssuedDocument;
use App\Models\DocumentSignature;
use App\Models\User;
use App\Notifications\SignatureRequired;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Exception;

class DocumentSigningService
{
    public function __construct(
        private DocumentIssuanceService $issuanceService,
        private RequestStatusService $statusService
    ) {}

    public function canSign(User $user, IssuedDocument $doc, string $roleTitle): bool
    {
        $adminRoles = ['admin', 'super_admin', 'academic_admin'];

        if (!$user->is_active || !in_array($user->role, $adminRoles)) {
            return false;
        }

        if ($doc->isFullySigned()) {
            return false;
        }

        if ($user->signer_role && $user->signer_role !== $roleTitle) {
            return false;
        }

        $alreadySigned = DocumentSignature::where('issued_document_id', $doc->id)
            ->where('role_title', $roleTitle)
            ->exists();

        if ($alreadySigned) {
            return false;
        }

        $currentSigner = $doc->getCurrentSigner();
        if ($currentSigner !== $roleTitle) {
            return false;
        }

        return true;
    }

    public function sign(User $user, IssuedDocument $doc, string $roleTitle, string $ipAddress): DocumentSignature
    {
        if (!in_array($roleTitle, $doc->getRequiredSigners())) {
            throw new Exception('هذا المنصب غير مطلوب للتوقيع على هذه الوثيقة.');
        }

        $alreadySigned = DocumentSignature::where('issued_document_id', $doc->id)
            ->where('role_title', $roleTitle)
            ->exists();

        if ($alreadySigned) {
            throw new Exception('تم توقيع هذه الوثيقة مسبقاً من هذا الدور');
        }

        $currentSigner = $doc->getCurrentSigner();
        if ($currentSigner !== $roleTitle) {
            throw new Exception('ليس دورك الحالي للتوقيع على هذه الوثيقة');
        }

        if (!$this->canSign($user, $doc, $roleTitle)) {
            throw new Exception('لا يمكنك التوقيع على هذا المنصب. قد يكون تم التوقيع عليه مسبقاً أو ليس لديك الصلاحية.');
        }

        $signature = DB::transaction(function () use ($user, $doc, $roleTitle, $ipAddress) {
            $signature = DocumentSignature::create([
                'issued_document_id' => $doc->id,
                'user_id' => $user->id,
                'role_title' => $roleTitle,
                'signed_at' => now(),
                'ip_address' => $ipAddress,
            ]);

            $doc->load('signatures');

            $this->finalizeIfComplete($doc);

            return $signature;
        });

        if ($doc->isFullySigned() && $doc->documentRequest->status === 'PENDING_SIGNATURES') {
            $this->statusService->transition(
                $doc->documentRequest,
                'ISSUED',
                'تم إصدار الوثيقة تلقائياً بعد اكتمال جميع التوقيعات.',
                $user->id
            );
        }

        $this->notifyCurrentSigner($doc);

        return $signature;
    }

    public function notifyCurrentSigner(IssuedDocument $doc): void
    {
        $currentSigner = $doc->getCurrentSigner();

        if (!$currentSigner) {
            return;
        }

        $signers = User::where('signer_role', $currentSigner)
            ->where('is_active', true)
            ->get();

        if ($signers->isEmpty()) {
            return;
        }

        foreach ($signers as $signer) {
            $alreadyNotified = DB::table('notifications')
                ->where('notifiable_type', User::class)
                ->where('notifiable_id', $signer->id)
                ->where('data->type', 'signature_required')
                ->where('data->issued_document_id', $doc->id)
                ->where('data->current_signer_role', $currentSigner)
                ->whereNull('read_at')
                ->exists();

            if (!$alreadyNotified) {
                $signer->notify(new SignatureRequired($doc, $currentSigner));
            }
        }
    }

    public function finalizeIfComplete(IssuedDocument $doc): void
    {
        if ($doc->isFullySigned()) {
            return;
        }

        $remaining = $doc->remainingSigners();

        if (empty($remaining)) {
            $this->issuanceService->finalizePdf($doc);
            $doc->update(['all_signed_at' => now()]);
        }
    }

    public function getPendingForUser(User $user, int $perPage = 15, ?string $search = null, ?int $documentTypeId = null, ?string $dateFrom = null, ?string $dateTo = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $adminRoles = ['admin', 'super_admin', 'academic_admin'];
        if (!$user->is_active || !in_array($user->role, $adminRoles)) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
        }

        $query = IssuedDocument::whereNull('all_signed_at')
            ->whereNotNull('document_request_id')
            ->whereHas('documentRequest', function ($q) {
                $q->where('status', 'PENDING_SIGNATURES');
            });

        if ($search) {
            $query->whereHas('documentRequest', function ($q) use ($search) {
                $q->where('tracking_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($documentTypeId) {
            $query->whereHas('documentRequest', function ($q) use ($documentTypeId) {
                $q->where('document_type_id', $documentTypeId);
            });
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $allDocs = $query->with(['documentRequest.user', 'documentRequest.documentType', 'signatures'])->get();

        $userRole = $user->signer_role;

        if (!$userRole) {
            $filtered = $allDocs->filter(function ($doc) {
                return $doc->getCurrentSigner() !== null;
            })->values();
        } else {
            $filtered = $allDocs->filter(function ($doc) use ($userRole) {
                return $doc->getCurrentSigner() === $userRole;
            })->values();
        }

        $page = (int) request()->get('page', 1);
        $total = $filtered->count();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $filtered->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function getCompletedDocuments(int $perPage = 15, ?string $search = null, ?int $documentTypeId = null, ?string $dateFrom = null, ?string $dateTo = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = IssuedDocument::whereNotNull('all_signed_at')
            ->whereNotNull('document_request_id');

        if ($search) {
            $query->whereHas('documentRequest', function ($q) use ($search) {
                $q->where('tracking_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($documentTypeId) {
            $query->whereHas('documentRequest', function ($q) use ($documentTypeId) {
                $q->where('document_type_id', $documentTypeId);
            });
        }

        if ($dateFrom) {
            $query->whereDate('all_signed_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('all_signed_at', '<=', $dateTo);
        }

        return $query->with(['documentRequest.user', 'documentRequest.documentType', 'signatures.user'])
            ->latest('all_signed_at')
            ->paginate($perPage);
    }
}
