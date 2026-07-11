<?php

namespace App\Services;

use App\Models\ApprovedGraduate;
use App\Models\IssuedDocument;
use App\Models\Employer;
use App\Models\Job;

class PublicPortalStatsService
{
    /**
     * Get statistics for the public homepage.
     *
     * @return array
     */
    public function getStats(): array
    {
        return [
            'approved_graduates_count' => ApprovedGraduate::count(),
            'issued_documents_count' => IssuedDocument::count(),
            'employers_count' => Employer::count(),
            'jobs_count' => Job::count(),
        ];
    }
}
