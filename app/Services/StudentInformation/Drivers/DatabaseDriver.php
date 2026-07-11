<?php

namespace App\Services\StudentInformation\Drivers;

use App\Contracts\StudentInformationProvider;
use App\Models\User;
use App\Models\GraduateAcademicRecord;

class DatabaseDriver implements StudentInformationProvider
{
    /**
     * Determine if academic record data exists for the given user.
     */
    public function hasAcademicRecord(User $user): bool
    {
        return $user->academicRecord()
            ->whereHas('levels.semesters.subjects')
            ->exists();
    }

    /**
     * Retrieve the academic record for the given user.
     */
    public function getAcademicRecord(User $user): ?GraduateAcademicRecord
    {
        return $user->academicRecord;
    }

    /**
     * Retrieve the academic record with levels, semesters, and subjects loaded.
     */
    public function getAcademicRecordWithDetails(User $user): ?GraduateAcademicRecord
    {
        return GraduateAcademicRecord::query()
            ->where('user_id', $user->id)
            ->with(['levels.semesters.subjects'])
            ->first();
    }
}
