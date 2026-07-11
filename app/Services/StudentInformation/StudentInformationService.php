<?php

namespace App\Services\StudentInformation;

use Illuminate\Support\Manager;
use App\Contracts\StudentInformationProvider;
use App\Models\User;
use App\Models\GraduateAcademicRecord;

class StudentInformationService extends Manager implements StudentInformationProvider
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('student_affairs.default', 'database');
    }

    /**
     * Create database driver instance.
     *
     * @return Drivers\DatabaseDriver
     */
    public function createDatabaseDriver(): Drivers\DatabaseDriver
    {
        return new Drivers\DatabaseDriver();
    }

    /**
     * Create Excel driver instance.
     *
     * @return Drivers\ExcelDriver
     */
    public function createExcelDriver(): Drivers\ExcelDriver
    {
        return new Drivers\ExcelDriver();
    }

    /**
     * Create API driver instance.
     *
     * @return Drivers\ApiDriver
     */
    public function createApiDriver(): Drivers\ApiDriver
    {
        return new Drivers\ApiDriver();
    }

    /**
     * Determine if academic record data exists for the given user.
     */
    public function hasAcademicRecord(User $user): bool
    {
        return $this->driver()->hasAcademicRecord($user);
    }

    /**
     * Retrieve the academic record for the given user.
     */
    public function getAcademicRecord(User $user): ?GraduateAcademicRecord
    {
        return $this->driver()->getAcademicRecord($user);
    }

    /**
     * Retrieve the academic record with levels, semesters, and subjects loaded.
     */
    public function getAcademicRecordWithDetails(User $user): ?GraduateAcademicRecord
    {
        return $this->driver()->getAcademicRecordWithDetails($user);
    }
}
