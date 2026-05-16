<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;

class JobPolicy
{
    public function manage(User $user, Job $job): bool
    {
        return $user->role === 'admin' || $user->id === $job->employer_id;
    }
}
