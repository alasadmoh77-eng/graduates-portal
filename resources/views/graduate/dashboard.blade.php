@extends('layouts.app')

@section('title', __('app.dashboard'))

@section('content')
<div class="container-fluid py-4">

    @if(!$hasAcademicRecord)
        <div class="alert alert-info d-flex align-items-center gap-3 rounded-4 shadow-sm border-0 mb-4">
            <div class="bg-info bg-opacity-25 p-3 rounded-circle">
                <i class="fas fa-info-circle fa-lg text-info"></i>
            </div>
            <div>
                <strong class="d-block mb-1">{{ __('app.no_academic_record_title') }}</strong>
                <p class="mb-0 small">{{ __('app.no_academic_record_yet') }}</p>
            </div>
        </div>
    @endif

    <div class="welcome-card mb-5 shadow-lg">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="academic-pill"><i class="fas fa-graduation-cap me-2"></i> {{ __('app.batch_2023_2024') }}</span>
                    <span class="academic-pill"><i class="fas fa-id-card me-2"></i> {{ Auth::user()->graduate?->university_id ?? Auth::user()->id }}</span>
                </div>
                <h1 class="display-5 fw-bold mb-2">{{ __('app.welcome_user') }} {{ Auth::user()->name }}</h1>
                <p class="fs-5 opacity-75 mb-4">{{ __('app.graduate_welcome_desc') }}</p>

                <div class="d-flex gap-3">
                    <a href="{{ route('graduate.documents.create') }}" class="btn btn-gradient quick-action-btn shadow-sm">
                        <i class="fas fa-plus-circle me-2"></i> {{ __('app.request_new_document') }}
                    </a>
                    <a href="{{ route('graduate.profile.show') }}" class="btn btn-light quick-action-btn text-primary">
                        <i class="fas fa-user-edit me-2"></i> {{ __('app.edit_profile') }}
                    </a>
                </div>
            </div>
            <div class="col-md-4 d-none d-md-block text-center">
                <div class="position-relative">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=105D82&color=fff&size=150" class="rounded-circle border border-4 border-white shadow-lg" alt="Profile">
                    <div class="position-absolute bottom-0 end-0 bg-success border border-4 border-white rounded-circle p-3 shadow-sm"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-3 mb-4">
            <div class="card stat-card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-chart-line fa-lg"></i>
                    </div>
                    <h6 class="text-muted mb-1 fw-bold">{{ __('app.gpa') }}</h6>
                    <h3 class="fw-bold mb-0">{{ $gpa ? number_format((float) $gpa, 2) . '%' : __('app.not_available') }}</h3>
                    @if($gpa)
                        <small class="text-success fw-bold"><i class="fas fa-caret-up"></i> {{ __('app.excellent') }}</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card stat-card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                    <h6 class="text-muted mb-1 fw-bold">{{ __('app.credit_hours') }}</h6>
                    <h3 class="fw-bold mb-0">{{ $totalCreditHours ?: __('app.not_available') }}</h3>
                    <small class="text-muted">{{ __('app.four_years_system') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card stat-card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="icon-box bg-info bg-opacity-10 text-info">
                        <i class="fas fa-file-signature fa-lg"></i>
                    </div>
                    <h6 class="text-muted mb-1 fw-bold">{{ __('app.active_requests') }}</h6>
                    <h3 class="fw-bold mb-0">{{ $activeRequestsCount }}</h3>
                    <small class="text-info fw-bold">{{ __('app.under_review') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card stat-card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-medal fa-lg"></i>
                    </div>
                    <h6 class="text-muted mb-1 fw-bold">{{ __('app.batch_rank') }}</h6>
                    <h3 class="fw-bold mb-0">{{ $academicRank ?: __('app.not_available') }}</h3>
                    @if($academicRank)
                        <small class="text-warning fw-bold">{{ __('app.first_honors') }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold m-0 text-primary"><i class="fas fa-file-alt me-2"></i> {{ __('app.ready_academic_documents') }}</h5>
                </div>
                <div class="card-body p-0">
                    @if($readyDocuments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 responsive-card-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 border-0">{{ __('app.document_name') }}</th>
                                        <th class="border-0">{{ __('app.status') }}</th>
                                        <th class="border-0">{{ __('app.issue_date') }}</th>
                                        <th class="border-0 text-end pe-4">{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($readyDocuments as $doc)
                                        <tr>
                                            <td class="ps-4" data-label="{{ __('app.document_name') }}">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="bg-primary bg-opacity-10 p-2 rounded text-primary">
                                                        <i class="fas fa-scroll"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ app()->getLocale() === 'ar' ? $doc->documentType->name_ar : $doc->documentType->name_en }}</div>
                                                        <div class="small text-muted">{{ $doc->tracking_code }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td data-label="{{ __('app.status') }}">
                                                <span class="status-badge bg-success bg-opacity-10 text-success">{{ __('app.document_status.' . $doc->status) }}</span>
                                            </td>
                                            <td data-label="{{ __('app.issue_date') }}">
                                                {{ $doc->issuedDocument?->issued_at ? $doc->issuedDocument->issued_at->format('Y/m/d') : $doc->updated_at->format('Y/m/d') }}
                                            </td>
                                            <td class="text-end pe-4" data-label="{{ __('app.actions') }}">
                                                <a href="{{ route('graduate.documents.show', $doc) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">{{ __('app.view') }}</a>
                                                @if($doc->issuedDocument && $doc->issuedDocument->pdf_path)
                                                    <a href="{{ route('graduate.documents.download', $doc) }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                                        <i class="fas fa-download me-1"></i> {{ __('app.download') }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3 text-muted opacity-50">
                                <i class="fas fa-file-alt fa-4x"></i>
                            </div>
                            <p class="text-muted mb-0">{{ __('app.no_ready_documents_yet') }}</p>
                        </div>
                    @endif
                    <div class="p-4 text-center">
                        <a href="{{ route('graduate.documents.index') }}" class="text-decoration-none fw-bold small">{{ __('app.view_all_documents_requests') }} <i class="fas fa-arrow-left ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-4 px-4">
                    <h5 class="fw-bold m-0 text-primary"><i class="fas fa-bullhorn me-2"></i> {{ __('app.latest_news_events') }}</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="d-flex gap-3 mb-4">
                        <div class="bg-warning bg-opacity-10 p-2 rounded text-warning h-100">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <div class="fw-bold small">{{ __('app.annual_graduation_ceremony') }}</div>
                            <p class="small text-muted mb-0">{{ __('app.attendance_deadline_desc') }}</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mb-2">
                        <div class="bg-info bg-opacity-10 p-2 rounded text-info h-100">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div>
                            <div class="fw-bold small">{{ __('app.open_employment_day') }}</div>
                            <p class="small text-muted mb-0">{{ __('app.employment_day_desc') }}</p>
                        </div>
                    </div>
                    <hr>
                    <a href="{{ route('graduate.events.index') }}" class="btn btn-outline-light text-primary w-100 fw-bold border-0">{{ __('app.view_all') }}</a>
                </div>
            </div>

            <div class="card border-0 bg-light rounded-4 shadow-sm">
                <div class="card-body p-4 text-center">
                    <div class="mb-3 text-primary opacity-25">
                        <i class="fas fa-qrcode fa-4x"></i>
                    </div>
                    <h6 class="fw-bold">{{ __('app.are_your_documents_official') }}</h6>
                    <p class="small text-muted">{{ __('app.qr_verification_desc') }}</p>
                    <a href="{{ route('verify.search') }}" class="btn btn-sm btn-outline-primary rounded-pill px-4">{{ __('app.try_verification') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
