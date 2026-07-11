@extends('layouts.app')

@section('title', $employer->company_name)

@section('content')
<div class="row mb-4">
    <div class="col-lg-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item">
                    <a href="{{ route('graduate.employers.index') }}">{{ __('app.partner_employers') }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $employer->company_name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <!-- Employer Profile Card -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 1rem;">
            <div class="card-body p-4 text-center">
                <div class="mb-4">
                    @if($employer->logo)
                        <img src="{{ asset('storage/' . $employer->logo) }}" alt="Logo" class="rounded border p-2 img-fluid" style="max-height: 120px; object-fit: cover;">
                    @else
                        <div class="bg-light text-primary rounded border p-4 d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-building fa-3x"></i>
                        </div>
                    @endif
                </div>

                <h4 class="fw-bold mb-2 text-dark">{{ $employer->company_name }}</h4>
                <p class="text-muted mb-4 small">{{ $employer->industry ?: __('app.not_available') }}</p>
                
                <hr class="my-4">

                <div class="text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">{{ __('app.employer_location') }}</label>
                        <p class="mb-0 text-dark fw-semibold"><i class="fas fa-map-marker-alt text-primary me-2"></i> {{ $employer->address ?: __('app.not_available') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">{{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone Number' }}</label>
                        <p class="mb-0 text-dark fw-semibold">
                            <i class="fas fa-phone text-primary me-2"></i>
                            @if($employer->phone)
                                <a href="tel:{{ $employer->phone }}" class="text-decoration-none text-primary">{{ $employer->phone }}</a>
                            @else
                                <span class="text-muted">{{ __('app.not_available') }}</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}</label>
                        <p class="mb-0 text-dark fw-semibold">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            @if($employer->company_email)
                                <a href="mailto:{{ $employer->company_email }}" class="text-decoration-none text-primary">{{ $employer->company_email }}</a>
                            @else
                                <span class="text-muted">{{ __('app.not_available') }}</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">{{ __('app.employer_website') }}</label>
                        <p class="mb-0 text-dark fw-semibold">
                            <i class="fas fa-globe text-primary me-2"></i>
                            @if($employer->website)
                                @php
                                    $webUrl = $employer->website;
                                    if (!preg_match("~^(?:f|ht)tps?://~i", $webUrl)) {
                                        $webUrl = "https://" . $webUrl;
                                    }
                                @endphp
                                <a href="{{ $webUrl }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-primary">{{ $employer->website }}</a>
                            @else
                                <span class="text-muted">{{ __('app.not_available') }}</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">{{ __('app.employer_joined_at') }}</label>
                        <p class="mb-0 text-dark fw-semibold"><i class="fas fa-calendar-alt text-primary me-2"></i> {{ $employer->created_at->format('Y-m-d') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">{{ __('app.employer_jobs_count') }}</label>
                        <p class="mb-0 text-dark fw-semibold"><i class="fas fa-briefcase text-primary me-2"></i> {{ $jobs->count() }}</p>
                    </div>
                </div>
                
                <a href="{{ route('graduate.employers.index') }}" class="btn btn-outline-secondary w-100 rounded-pill mt-3 py-2 fw-bold">
                    <i class="fas fa-arrow-right me-1"></i> {{ app()->getLocale() === 'ar' ? 'العودة للقائمة' : 'Back to Directory' }}
                </a>
            </div>
        </div>
    </div>

    <!-- Employer Description & Jobs List -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-info-circle text-primary me-2"></i> {{ app()->getLocale() === 'ar' ? 'نبذة عن الجهة' : 'About the Employer' }}</h5>
                <div class="text-muted" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;">{{ $employer->description ?: (app()->getLocale() === 'ar' ? 'لا توجد نبذة تعريفية متاحة لهذه الجهة حالياً.' : 'No description available for this employer at the moment.') }}</div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-bullhorn text-primary me-2"></i> {{ app()->getLocale() === 'ar' ? 'فرص العمل النشطة المعلنة' : 'Active Job Opportunities' }}</h5>
                
                <div class="row">
                    @forelse($jobs as $job)
                        <div class="col-12 mb-3">
                            <div class="border rounded p-3 hover-shadow-sm transition d-flex flex-wrap justify-content-between align-items-center {{ $job->is_filled ? 'job-card-filled' : '' }}">
                                <div>
                                    <h6 class="fw-bold mb-1"><a href="{{ route('graduate.jobs.show', $job->id) }}" class="text-decoration-none text-dark">{{ $job->title }}</a></h6>
                                    <div class="d-flex flex-wrap gap-2 text-muted small mt-2">
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill">{{ $job->job_type }}</span>
                                        @if($job->is_filled)
                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">{{ app()->getLocale() === 'ar' ? 'تم شغلها' : 'Filled' }}</span>
                                        @else
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">{{ app()->getLocale() === 'ar' ? 'متاحة' : 'Available' }}</span>
                                        @endif
                                        <span><i class="fas fa-map-marker-alt me-1"></i> {{ $job->location }}</span>
                                        <span><i class="fas fa-clock me-1"></i> {{ __('app.deadline') }}: {{ $job->deadline->format('Y-m-d') }}</span>
                                    </div>
                                </div>
                                <div class="mt-3 mt-sm-0">
                                    <a href="{{ route('graduate.jobs.show', $job->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">
                                        @if($job->is_filled)
                                            {{ app()->getLocale() === 'ar' ? 'التفاصيل (غير متاحة)' : 'Details (Unavailable)' }}
                                        @else
                                            {{ app()->getLocale() === 'ar' ? 'التفاصيل والتقديم' : 'Details & apply' }}
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4">
                            <div class="text-muted mb-2"><i class="fas fa-briefcase fa-2x"></i></div>
                            <p class="text-muted small mb-0">{{ app()->getLocale() === 'ar' ? 'لا توجد وظائف نشطة معلنة حالياً من قبل هذه الجهة.' : 'No active jobs announced by this employer currently.' }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
