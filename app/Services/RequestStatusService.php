<?php

namespace App\Services;

use App\Models\DocumentRequest;
use App\Models\RequestStatusLog;
use Illuminate\Support\Facades\DB;
use Exception;

class RequestStatusService
{
    const STATUS_SUBMITTED = 'SUBMITTED';
    const STATUS_UNDER_REVIEW = 'UNDER_REVIEW';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_READY = 'READY';
    const STATUS_ISSUED = 'ISSUED';

    /**
     * Allowed transitions Map
     */
    protected $transitions = [
        self::STATUS_SUBMITTED => [self::STATUS_UNDER_REVIEW],
        self::STATUS_UNDER_REVIEW => [self::STATUS_APPROVED, self::STATUS_REJECTED],
        self::STATUS_APPROVED => [self::STATUS_READY],
        self::STATUS_READY => [self::STATUS_ISSUED],
        self::STATUS_REJECTED => [], // Terminal state by default
        self::STATUS_ISSUED => [],   // Terminal state
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

            // 5. Notify User
            $request->user->notify(new \App\Notifications\RequestStatusChanged($request, $fromStatus, $toStatus));
        });
    }

    /**
     * Get available transitions for current status
     */
    public function getAvailableTransitions(string $currentStatus): array
    {
        return $this->transitions[$currentStatus] ?? [];
    }
}
