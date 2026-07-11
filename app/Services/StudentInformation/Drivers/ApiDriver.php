<?php

namespace App\Services\StudentInformation\Drivers;

use App\Contracts\StudentInformationProvider;
use App\Models\User;
use App\Models\GraduateAcademicRecord;

class ApiDriver implements StudentInformationProvider
{
    /**
     * Determine if academic record data exists for the given user.
     */
    public function hasAcademicRecord(User $user): bool
    {
        // Skeletal implementation for future API / Oracle database integration
        return false;
    }

    /**
     * Retrieve the academic record for the given user.
     */
    public function getAcademicRecord(User $user): ?GraduateAcademicRecord
    {
        return null;
    }

    /**
     * Retrieve the academic record with levels, semesters, and subjects loaded.
     */
    public function getAcademicRecordWithDetails(User $user): ?GraduateAcademicRecord
    {
        return null;
    }
}
