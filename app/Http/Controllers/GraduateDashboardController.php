<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GraduateDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $academicRecord = $user->academicRecord;

        $gpa = null;
        $totalCreditHours = 0;
        $academicRank = null;
        $hasAcademicRecord = false;

        if ($academicRecord) {
            $hasAcademicRecord = true;
            $gpa = $academicRecord->gpa;
            $academicRank = $academicRecord->overall_rating ?? $academicRecord->honors_rank;

            $totalCreditHours = $academicRecord->levels()
                ->with('semesters.subjects')
                ->get()
                ->flatMap->semesters
                ->flatMap->subjects
                ->sum('credit_hours');
        }

        $activeRequestsCount = $user->documentRequests()
            ->whereIn('status', ['SUBMITTED', 'UNDER_REVIEW', 'APPROVED'])
            ->count();

        $issuedDocumentsCount = $user->documentRequests()
            ->whereIn('status', ['READY', 'ISSUED'])
            ->whereHas('issuedDocument')
            ->count();

        $readyDocuments = $user->documentRequests()
            ->with(['documentType', 'issuedDocument'])
            ->whereIn('status', ['READY', 'ISSUED'])
            ->whereHas('issuedDocument')
            ->latest()
            ->get();

        return view('graduate.dashboard', compact(
            'gpa',
            'totalCreditHours',
            'academicRank',
            'activeRequestsCount',
            'issuedDocumentsCount',
            'readyDocuments',
            'hasAcademicRecord'
        ));
    }
}
