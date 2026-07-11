<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\GraduateAcademicRecord;

interface StudentInformationProvider
{
    /**
     * Determine if academic record data exists for the given user.
     */
    public function hasAcademicRecord(User $user): bool;

    /**
     * Retrieve the academic record for the given user.
     */
    public function getAcademicRecord(User $user): ?GraduateAcademicRecord;

    /**
     * Retrieve the academic record with levels, semesters, and subjects loaded.
     */
    public function getAcademicRecordWithDetails(User $user): ?GraduateAcademicRecord;
}
