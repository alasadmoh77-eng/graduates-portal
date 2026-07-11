@php
    $arName = trim((string) ($academic_record->student_name_ar ?? '')) !== ''
        ? $academic_record->student_name_ar
        : ($request->user->name ?? '—');
    $arUid = $academic_record->university_number ?: ($request->user->graduate->university_id ?? '—');
    
    // Resolve dynamic degree name from majors database
    $arDegree = $academic_record->degree_ar
        ?: ($request->user->graduate->major->degree_name_ar
            ?: $academic_record->degree_ar);
            
    $arMajor = $request->user->graduate->major->name_ar ?? '—';
    $arFaculty = $request->user->graduate->major->faculty->name_ar ?? '—';
    $arGradY = $academic_record->graduation_year_label ?: ($request->user->graduate->graduation_year ?? '—');
    $arRating = $academic_record->overall_rating ?: '—';
    $arGpa = $academic_record->gpa ?: '—';
    $hasDisqualifying = \App\Helpers\AcademicHelper::hasHonorDisqualifyingGrade($academic_record);
    $arHonors = ($academic_record && !$hasDisqualifying) ? ($academic_record->honors_rank ?: '—') : '—';
    $arEnrollY = $academic_record->enrollment_year_label ?: '—';
    $arExamSession = $academic_record->exam_session ?: '—';
    
    $cleanGpa = rtrim($arGpa, '%');
    $honorsStr = ($arHonors && $arHonors !== '—' && $arHonors !== 'بدون' && $arHonors !== 'لا يوجد') ? " " . $arHonors : "";
    $sessionPhrase = \App\Helpers\AcademicHelper::formatArabicSession($arExamSession);
@endphp

@extends('pdf.documents.layout')

@section('title', ar('شهادة الدرجات والتقديرات'))

@section('intro')
    <div class="intro-container">
        <div class="intro-text" style="text-align: center;">
            <div style="margin-bottom: 2px;">{{ ar('تشهد جامعة إقليم سبأ بأن الطالب/ ' . $arName . ' المقيد بالرقم الجامعي ' . $arUid) }}</div>
            <div style="margin-bottom: 2px;">{{ ar('قد التحق بالجامعة في العام الدراسي ' . $arEnrollY . '، بكلية ' . $arFaculty . '، تخصص ' . $arMajor . '،') }}</div>
            <div style="margin-bottom: 2px;">{{ ar('وقد أكمل بنجاح جميع متطلبات التخرج لنيل درجة ' . $arDegree . '،') }}</div>
            <div>{{ ar('وتخرج في ' . $sessionPhrase . ' للعام الجامعي ' . $arGradY . '، بمعدل تراكمي ' . $cleanGpa . '%، وبتقدير عام ' . $arRating . $honorsStr . '.') }}</div>
        </div>
    </div>
@endsection


