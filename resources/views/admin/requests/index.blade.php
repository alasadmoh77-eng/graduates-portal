@extends('layouts.app')

@section('title', __('app.admin_requests_index_title'))


@section('content')
<div class="container-fluid py-4 px-lg-5">
    
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-primary mb-1"><i class="fas fa-inbox me-2"></i> {{ __('app.admin_requests_index_heading') }}</h2>
            <p class="text-muted mb-0">لوحة التحكم المركزية لمعالجة وإصدار وثائق الخريجين.</p>
        </div>
        <div class="bg-white px-4 py-2 rounded-pill shadow-sm border text-primary fw-bold">
            إجمالي الطلبات: <span class="badge bg-primary rounded-pill ms-1">{{ $requests->total() }}</span>
        </div>
    </div>

    <!-- Enhanced Filters -->
    <div class="filter-card mb-4">
        <div class="card-header bg-white border-bottom p-3">
            <h6 class="fw-bold mb-0 text-secondary"><i class="fas fa-filter me-2"></i> أدوات التصفية والبحث</h6>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.requests.index') }}" method="GET" class="row g-3">
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <label class="form-label small fw-bold text-muted">البحث العام (الاسم، الرمز)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control custom-input border-start-0 ps-0" value="{{ request('search') }}" placeholder="{{ __('app.admin_filter_search_placeholder') }}">
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6">
                    <label class="form-label small fw-bold text-muted">{{ __('app.status') }}</label>
                    <select name="status" class="form-select custom-input">
                        <option value="">{{ __('app.admin_filter_all_statuses') }}</option>
                        @foreach(['SUBMITTED', 'UNDER_REVIEW', 'APPROVED', 'PENDING_SIGNATURES', 'REJECTED', 'READY', 'ISSUED'] as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ __('app.document_status.'.$status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6">
                    <label class="form-label small fw-bold text-muted">{{ __('app.documents_document_type') }}</label>
                    <select name="document_type_id" class="form-select custom-input">
                        <option value="">{{ __('app.admin_filter_all_types') }}</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ request('document_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-1 col-lg-4 col-md-6">
                    <label class="form-label small fw-bold text-muted">{{ __('app.language') }}</label>
                    <select name="language" class="form-select custom-input">
                        <option value="">الكل</option>
                        <option value="AR" {{ request('language') === 'AR' ? 'selected' : '' }}>العربية (AR)</option>
                        <option value="EN" {{ request('language') === 'EN' ? 'selected' : '' }}>الإنكليزية (EN)</option>
                    </select>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6">
                    <label class="form-label small fw-bold text-muted">من تاريخ</label>
                    <input type="text" name="date_from" class="form-control custom-input date-picker-input" value="{{ request('date_from') }}" placeholder="YYYY-MM-DD" dir="ltr" lang="en" readonly autocomplete="off">
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6">
                    <label class="form-label small fw-bold text-muted">إلى تاريخ</label>
                    <div class="d-flex gap-2">
                        <input type="text" name="date_to" class="form-control custom-input date-picker-input" value="{{ request('date_to') }}" placeholder="YYYY-MM-DD" dir="ltr" lang="en" readonly autocomplete="off">
                        <button type="submit" class="filter-btn flex-shrink-0" title="تطبيق التصفية"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table custom-table table-hover align-middle mb-0 text-center">
                <thead>
                    <tr>
                        <th class="text-start ps-4">{{ __('app.documents_col_tracking') }}</th>
                        <th class="text-start">{{ __('app.admin_col_graduate') }}</th>
                        <th>{{ __('app.documents_col_type') }}</th>
                        <th>{{ __('app.language') }}</th>
                        <th>{{ __('app.status') }}</th>
                        <th>{{ __('app.admin_col_submitted') }}</th>
                        <th class="text-end pe-4">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td class="text-start ps-4">
                                <div class="font-monospace fw-bold text-primary bg-light px-2 py-1 rounded d-inline-block">{{ $request->tracking_code }}</div>
                            </td>
                            <td class="text-start">
                                <div class="text-dark fw-bold d-flex align-items-center gap-2">
                                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <div>
                                        <div class="mb-0">{{ $request->user->name }}</div>
                                        <div class="small text-muted fw-normal">{{ $request->user->graduate->university_id ?? 'بدون رقم' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark small">
                                    <i class="fas {{ $request->documentType->code === 'ACADEMIC_RECORD' ? 'fa-graduation-cap' : 'fa-award' }} text-secondary me-1"></i>
                                    {{ $request->documentType->name_ar }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">{{ $request->language }}</span>
                            </td>
                            <td>
                                <x-status-badge :status="$request->status" />
                            </td>
                            <td>
                                <div class="small fw-bold text-muted">{{ $request->created_at->format('Y-m-d') }}</div>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.requests.show', $request) }}" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-bold">
                                    معالجة <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-0">
                                <x-empty-state
                                    icon="fa-inbox"
                                    :title="__('app.no_recent_requests')"
                                    :message="__('app.no_requests_match')"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
            <div class="card-footer bg-white p-4 d-flex justify-content-center border-top">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
