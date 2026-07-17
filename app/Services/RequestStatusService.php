<?php

namespace App\Services;

use App\Models\DocumentRequest;
use App\Models\RequestStatusLog;
use Illuminate\Support\Facades\DB;
use Exception;

class RequestStatusService
{
    protected $paymentBlockedStatuses = ['APPROVED', 'READY', 'ISSUED'];
    const STATUS_SUBMITTED = 'SUBMITTED';
    const STATUS_UNDER_REVIEW = 'UNDER_REVIEW';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_PENDING_SIGNATURES = 'PENDING_SIGNATURES';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_READY = 'READY';
    const STATUS_ISSUED = 'ISSUED';

    /**
     * Allowed transitions Map
     */
    protected $transitions = [
        self::STATUS_SUBMITTED => [self::STATUS_UNDER_REVIEW],
        self::STATUS_UNDER_REVIEW => [self::STATUS_APPROVED, self::STATUS_REJECTED],
        self::STATUS_APPROVED => [self::STATUS_PENDING_SIGNATURES],
        self::STATUS_PENDING_SIGNATURES => [self::STATUS_READY, self::STATUS_REJECTED, self::STATUS_ISSUED],
        self::STATUS_READY => [self::STATUS_ISSUED, self::STATUS_PENDING_SIGNATURES],
        self::STATUS_REJECTED => [],
        self::STATUS_ISSUED => [self::STATUS_PENDING_SIGNATURES],
    ];

    /**
     * Perform a status transition
     */
    public function transition(DocumentRequest $request, string $toStatus, ?string $note, int $adminId): void
    {
        $fromStatus = $request->status;

        // 1. Validate Transition
        if (!in_array($toStatus, $this->transitions[$fromStatus] ?? [])) {
            throw new Exception("Invalid transition from {$fromStatus} to {$toStatus}.");
        }

        // 2. Additional rules
        if ($toStatus === self::STATUS_REJECTED && empty($note)) {
            throw new Exception("A note is required for rejections.");
        }

        // 3. Signature completion check - block READY if not all signed
        if ($toStatus === self::STATUS_READY && $fromStatus === self::STATUS_PENDING_SIGNATURES) {
            $issuedDoc = $request->issuedDocument;
            if (!$issuedDoc || !$issuedDoc->all_signed_at) {
                throw new Exception('لا يمكن تغيير الحالة إلى جاهز قبل اكتمال جميع التوقيعات. يرجى إكمال التوقيعات أولاً.');
            }
        }

        // 4. Payment check - block approval/ready/issued if payment is required but not approved
        if (in_array($toStatus, $this->paymentBlockedStatuses)) {
            if ($request->payment_required && $request->payment_status !== 'approved') {
                throw new Exception(__('app.payment_must_be_approved'));
            }
        }

        DB::transaction(function () use ($request, $fromStatus, $toStatus, $note, $adminId) {
            // 3. Create Log
            RequestStatusLog::create([
                'document_request_id' => $request->id,
                'admin_id' => $adminId,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'note' => $note,
                'created_at' => now(),
            ]);

            // 4. Update Request
            $request->update([
                'status' => $toStatus,
                'admin_note' => $note
            ]);

            // 5. Notify User (only for statuses that affect the graduate's request outcome)
            $notifyStatuses = ['APPROVED', 'REJECTED', 'READY', 'ISSUED'];
            if (in_array($toStatus, $notifyStatuses)) {
                $request->user->notify(new \App\Notifications\RequestStatusChanged($request, $fromStatus, $toStatus));
            }
        });
    }

    /**
     * Centralized transition of a request to UNDER_REVIEW (Academic Review) status.
     * Restricts multiple transitions, creates status logs, and notifies active academic admins.
     */
    public function moveToAcademicReview(DocumentRequest $request, ?string $note, int $operatorId): void
    {
        $fromStatus = $request->status;
        $toStatus = self::STATUS_UNDER_REVIEW;

        if ($fromStatus === $toStatus) {
            return;
        }

        DB::transaction(function () use ($request, $fromStatus, $toStatus, $note, $operatorId) {
            // 1. Update Request
            $request->update([
                'status' => $toStatus,
                'admin_note' => $note
            ]);

            // 2. Create Log
            RequestStatusLog::create([
                'document_request_id' => $request->id,
                'admin_id' => $operatorId,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'note' => $note,
                'created_at' => now(),
            ]);

            // 3. Resolve active academic admins and send notification exactly once
            $academicAdmins = \App\Models\User::where('role', 'academic_admin')
                ->where('is_active', true)
                ->get();

            if ($academicAdmins->isNotEmpty()) {
                // Prevent duplicate notifications for this request
                $alreadyNotifiedIds = DB::table('notifications')
                    ->where('notifiable_type', \App\Models\User::class)
                    ->whereIn('notifiable_id', $academicAdmins->pluck('id'))
                    ->where('data->type', 'academic_review')
                    ->where('data->document_request_id', $request->id)
                    ->pluck('notifiable_id')
                    ->toArray();

                $usersToNotify = $academicAdmins->reject(function ($u) use ($alreadyNotifiedIds) {
                    return in_array($u->id, $alreadyNotifiedIds);
                });

                if ($usersToNotify->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send($usersToNotify, new \App\Notifications\RequestUnderReview($request));
                }
            } else {
                \Illuminate\Support\Facades\Log::warning('No active academic_admin users found to notify about request academic review.');
            }
        });
    }

    /**
     * Get available transitions for current status
     */
    public function getAvailableTransitions(string $currentStatus): array
    {
        $transitions = $this->transitions[$currentStatus] ?? [];

        if ($currentStatus === self::STATUS_APPROVED) {
            $transitions = array_values(array_diff($transitions, [self::STATUS_PENDING_SIGNATURES]));
        }

        if ($currentStatus === self::STATUS_PENDING_SIGNATURES) {
            $transitions = array_values(array_diff($transitions, [self::STATUS_ISSUED]));
        }

        if (in_array($currentStatus, [self::STATUS_READY, self::STATUS_ISSUED])) {
            $transitions = array_values(array_diff($transitions, [self::STATUS_PENDING_SIGNATURES]));
        }

        return $transitions;
    }
}
