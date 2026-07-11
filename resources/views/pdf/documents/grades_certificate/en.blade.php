@php
    $enName = trim((string) ($academic_record->student_name_en ?? '')) !== ''
        ? $academic_record->student_name_en
        : ($request->user->name ?? '—');
    $enUid = $academic_record->university_number ?: ($request->user->graduate->university_id ?? '—');
    
    // Resolve dynamic degree name from majors database or helpers
    $enDegree = $academic_record->degree_en
        ?: ($request->user->graduate->major->degree_name_en
            ?: \App\Support\AcademicRecordEnglishPdf::degree($academic_record->degree_en, $academic_record->degree_ar));
            
    $enMajor = \App\Support\AcademicRecordEnglishPdf::majorName(
        $request->user->graduate->major->name_en ?? null,
        $request->user->graduate->major->name_ar ?? null
    );
    $enFaculty = $request->user->graduate->major->faculty->name_en
        ?? $request->user->graduate->major->faculty->name_ar
        ?? '—';
    $enGradY = $academic_record->graduation_year_label ?: ($request->user->graduate->graduation_year ?? '—');
    $enRating = \App\Support\AcademicRecordEnglishPdf::rating($academic_record->overall_rating);
    $enGpa = $academic_record->gpa ?: '—';
    $hasDisqualifying = \App\Helpers\AcademicHelper::hasHonorDisqualifyingGrade($academic_record);
    $rawHonors = ($academic_record && !$hasDisqualifying) ? $academic_record->honors_rank : null;
    $enHonors = \App\Support\AcademicRecordEnglishPdf::honors($rawHonors);
    $enEnrollY = $academic_record->enrollment_year_label ?: '—';
    $enExamSession = \App\Support\AcademicRecordEnglishPdf::examSession($academic_record->exam_session);
    $cleanGpa = rtrim($enGpa, '%');
@endphp

@extends('pdf.documents.layout_en')

@section('title', 'Grades & Estimates Certificate')

@section('intro')
    <div class="intro-container">
        <div class="intro-text">
            Saba Region University, based on the academic records of the <strong>{{ $enFaculty }}</strong>,
            hereby certifies that Mr/Ms: <strong>{{ $enName }}</strong>
            registered under University ID: <strong>{{ $enUid }}</strong>, 
            enrolled in the academic year: <strong>{{ $enEnrollY }}</strong>.
            Has successfully completed all academic requirements for graduation to be awarded the degree of: 
            <strong>{{ $enDegree }}</strong> in the major of <strong>{{ $enMajor }}</strong>.
            Graduated during the <strong>{{ $enExamSession }}</strong> of the academic year <strong>{{ $enGradY }}</strong>
            with a cumulative average of <strong>{{ $cleanGpa }}%</strong> and an overall rating of <strong>{{ $enRating }}</strong>.
        </div>
    </div>
@endsection
