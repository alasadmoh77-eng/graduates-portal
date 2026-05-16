@extends('layouts.app')

@section('title', $job->title)

@section('content')
@php
    $g = auth()->user()->graduate;
    $profileCvReady = $g && $g->cv_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($g->cv_path);
@endphp
@if(!$profileCvReady)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ __('app.profile_cv_hint_jobs') }}
        <a href="{{ route('graduate.profile.edit') }}" class="alert-link fw-bold">{{ __('app.edit_profile') }}</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<div class="row mb-4">
    <div class="col-lg-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item">
                    <a href="{{ route('graduate.jobs.index') }}">{{ __('app.browse_jobs') }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($job->title, 50) }}</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">{{ $job->job_type }}</span>
            <small class="text-muted"><i class="fas fa-clock me-1"></i> {{ __('app.deadline') }}: {{ $job->deadline->format('Y-m-d') }}</small>
        </div>
        <h2 class="fw-bold mb-3">{{ $job->title }}</h2>
        <div class="d-flex align-items-start mb-4">
            <div class="flex-shrink-0">
                <div class="bg-light p-2 rounded">
                    <i class="fas fa-building text-primary"></i>
                </div>
            </div>
            <div class="ms-3">
                <h6 class="mb-0 fw-bold">{{ $job->employer->name }}</h6>
                <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i> {{ $job->location }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">{{ __('app.description') }}</h5>
                <div class="text-muted" style="white-space: pre-wrap;">{{ $job->description }}</div>
                @if($job->requirements)
                    <hr class="my-4">
                    <h5 class="fw-bold mb-3">{{ __('app.requirements') }}</h5>
                    <div class="text-muted" style="white-space: pre-wrap;">{{ $job->requirements }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 1rem;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">{{ __('app.apply') }}</h5>
                <form action="{{ route('graduate.jobs.apply', $job) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">رسالة التقديم (Cover Letter)</label>
                        <textarea name="cover_letter" class="form-control @error('cover_letter') is-invalid @enderror" rows="5" placeholder="اشرح باختصار لماذا أنت مهتم بهذه الوظيفة...">{{ old('cover_letter') }}</textarea>
                        @error('cover_letter')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">تحميل السيرة الذاتية (CV)</label>
                        <input type="file" name="cv_file" class="form-control @error('cv_file') is-invalid @enderror" accept=".pdf,.doc,.docx">
                        <small class="text-muted d-block mt-1">
                            @if($profileCvReady)
                                {{ app()->getLocale() === 'ar' ? 'إذا لم ترفع ملفاً جديداً، سيتم استخدام السيرة الذاتية المحفوظة في ملفك الشخصي.' : 'If you do not attach a new file, the CV saved on your profile will be used.' }}
                            @else
                                {{ __('app.cv_required_for_application') }}
                            @endif
                        </small>
                        @error('cv_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">إرسال الطلب</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
