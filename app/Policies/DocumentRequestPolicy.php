<?php

namespace App\Policies;

use App\Models\DocumentRequest;
use App\Models\User;

class DocumentRequestPolicy
{
    public function view(User $user, DocumentRequest $documentRequest): bool
    {
        return $user->role === 'admin' || $user->id === $documentRequest->graduate_id;
    }

    public function update(User $user, DocumentRequest $documentRequest): bool
    {
        // Only admin can process/update status
        return $user->role === 'admin';
    }
}
