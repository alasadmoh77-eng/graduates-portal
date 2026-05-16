@extends('layouts.app')

@section('title', 'لوحة تحكم الخريج')


@section('content')
<div class="container-fluid py-4">
    
    <!-- 1. Welcome Banner -->
    <div class="welcome-card mb-5 shadow-lg">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="academic-pill"><i class="fas fa-graduation-cap me-2"></i> {{ __('app.batch_2023_2024') }}</span>
                    <span class="academic-pill"><i class="fas fa-id-card me-2"></i> {{ Auth::user()->id ?? '202021001' }}</span>
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
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=d4af37&color=fff&size=150" class="rounded-circle border border-4 border-white shadow-lg" alt="Profile">
                    <div class="position-absolute bottom-0 end-0 bg-success border border-4 border-white rounded-circle p-3 shadow-sm"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Academic Stats Row -->
    <div class="row mb-5">
        <div class="col-md-3 mb-4">
            <div class="card stat-card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-chart-line fa-lg"></i>
                    </div>
                    <h6 class="text-muted mb-1 fw-bold">{{ __('app.gpa') }}</h6>
                    <h3 class="fw-bold mb-0">88.02%</h3>
                    <small class="text-success fw-bold"><i class="fas fa-caret-up"></i> {{ __('app.excellent') }}</small>
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
                    <h3 class="fw-bold mb-0">142 ساعة</h3>
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
                    <h3 class="fw-bold mb-0">02</h3>
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
                    <h3 class="fw-bold mb-0">{{ __('app.first_rank') }}</h3>
                    <small class="text-warning fw-bold">{{ __('app.first_honors') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- 3. Documents & Records Section -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold m-0 text-primary"><i class="fas fa-file-alt me-2"></i> {{ __('app.ready_academic_documents') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 border-0">{{ __('app.document_name') }}</th>
                                    <th class="border-0">{{ __('app.status') }}</th>
                                    <th class="border-0">{{ __('app.issue_date') }}</th>
                                    <th class="border-0 text-end pe-4">{{ __('app.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-primary bg-opacity-10 p-2 rounded text-primary">
                                                <i class="fas fa-scroll"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ __('app.grades_certificate') }}</div>
                                                <div class="small text-muted">{{ __('app.certified_electronic_copy') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="status-badge bg-success bg-opacity-10 text-success">{{ __('app.ready_for_download') }}</span></td>
                                    <td>2024/04/01</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('graduate.documents.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">{{ __('app.view') }}</a>
                                        <button class="btn btn-sm btn-primary rounded-pill px-3"><i class="fas fa-download me-1"></i> {{ __('app.download') }}</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-primary bg-opacity-10 p-2 rounded text-primary">
                                                <i class="fas fa-clipboard-list"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ __('app.academic_record_doc') }}</div>
                                                <div class="small text-muted">{{ __('app.annual_review_copy') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="status-badge bg-success bg-opacity-10 text-success">{{ __('app.ready_for_download') }}</span></td>
                                    <td>2024/04/05</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('graduate.documents.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">{{ __('app.view') }}</a>
                                        <button class="btn btn-sm btn-primary rounded-pill px-3"><i class="fas fa-download me-1"></i> {{ __('app.download') }}</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 text-center">
                        <a href="{{ route('graduate.documents.index') }}" class="text-decoration-none fw-bold small">{{ __('app.view_all_documents_requests') }} <i class="fas fa-arrow-left ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Quick Sidebar -->
        <div class="col-lg-4">
            <!-- News/Events Card -->
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

            <!-- Verification Helper Card -->
            <div class="card border-0 bg-light rounded-4 shadow-sm">
                <div class="card-body p-4 text-center">
                    <div class="mb-3 text-primary opacity-25">
                        <i class="fas fa-qrcode fa-4x"></i>
                    </div>
                    <h6 class="fw-bold">{{ __('app.are_your_documents_official') }}</h6>
                    <p class="small text-muted">{{ __('app.qr_verification_desc') }}</p>
                    <a href="{{ route('verify.show') }}" class="btn btn-sm btn-outline-primary rounded-pill px-4">{{ __('app.try_verification') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
