<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobApplication;

class JobService
{
    public function applyForJob(int $jobId, int $graduateId, ?string $coverLetter): JobApplication
    {
        // Get graduate's current CV to snapshot it
        $graduate = \App\Models\User::find($graduateId)->graduate;
        
        return JobApplication::create([
            'job_id' => $jobId,
            'graduate_id' => $graduateId,
            'cover_letter' => $coverLetter,
            'cv_path' => $graduate->cv_path, // Snapshot
            'status' => 'new'
        ]);
    }
}
