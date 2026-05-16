@extends('layouts.app')

@section('title', __('app.browse_jobs'))

@section('content')
@php
    $g = auth()->user()->graduate;
    $profileCvReady = $g && $g->cv_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($g->cv_path);
@endphp
@if(!$profileCvReady)
    <x-alert-message type="warning">
        {{ __('app.profile_cv_hint_jobs') }}
        <a href="{{ route('graduate.profile.edit') }}" class="alert-link fw-bold">{{ __('app.edit_profile') }}</a>
    </x-alert-message>
@endif
<div class="mb-4">
    <x-page-header 
        :title="__('app.browse_jobs')"
        subtitle="Explore career opportunities shared by our partners."
        icon="fa-search"
    />
</div>

<div class="row">
    @forelse($jobs as $job)
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">{{ $job->job_type }}</span>
                        <small class="text-muted"><i class="fas fa-clock me-1"></i> Deadline: {{ $job->deadline->format('Y-m-d') }}</small>
                    </div>
                    <h4 class="fw-bold mb-2">
                        <a href="{{ route('graduate.jobs.show', $job) }}" class="text-decoration-none text-dark">{{ $job->title }}</a>
                    </h4>
                    <p class="text-muted mb-4">{{ Str::limit($job->description, 150) }}</p>
                    
                    <div class="d-flex align-items-center mb-4">
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
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('graduate.jobs.show', $job) }}" class="btn btn-outline-primary rounded-pill py-2 fw-bold">
                            {{ app()->getLocale() === 'ar' ? 'التفاصيل والتقديم' : 'Details & apply' }}
                        </a>
                        <button type="button" class="btn btn-primary rounded-pill py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#applyModal{{ $job->id }}">
                            {{ __('app.apply') }}
                        </button>
                    </div>
                    
                    <!-- Apply Modal -->
                    <div class="modal fade" id="applyModal{{ $job->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content border-0">
                                <form action="{{ route('graduate.jobs.apply', $job->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">التقديم على وظيفة: {{ $job->title }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">رسالة التقديم (Cover Letter)</label>
                                            <textarea name="cover_letter" class="form-control @error('cover_letter') is-invalid @enderror" rows="4" placeholder="اشرح باختصار لماذا أنت مهتم بهذه الوظيفة...">{{ old('cover_letter') }}</textarea>
                                            @error('cover_letter')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">تحميل السيرة الذاتية (CV)</label>
                                            <input type="file" name="cv_file" class="form-control @error('cv_file') is-invalid @enderror" accept=".pdf,.doc,.docx">
                                            <small class="text-muted d-block mt-1">
                                                @if($profileCvReady)
                                                    {{ app()->getLocale() === 'ar' ? 'إذا لم ترفع ملفاً جديداً، تُستخدم السيرة الذاتية من ملفك الشخصي.' : 'If you skip a new file, your profile CV will be used.' }}
                                                @else
                                                    {{ __('app.cv_required_for_application') }}
                                                @endif
                                            </small>
                                            @error('cv_file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إغلاق</button>
                                        <button type="submit" class="btn btn-primary px-4">إرسال الطلب</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <x-empty-state
                icon="fa-briefcase"
                :title="__('app.no_active_jobs')"
                :message="__('app.no_active_jobs_hint')"
            />
        </div>
    @endforelse
</div>
@endsection
