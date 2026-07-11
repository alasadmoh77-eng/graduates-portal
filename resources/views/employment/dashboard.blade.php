@extends('layouts.app')
@section('title', 'لوحة تحكم مسؤول التوظيف')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">لوحة تحكم مسؤول التوظيف</h1>
        <p class="text-muted mb-0">مرحباً، {{ Auth::user()->name }}</p>
    </div>
    <a href="{{ route('admin.employment.analytics') }}" class="btn btn-primary rounded-pill px-4">
        <i class="fas fa-chart-bar me-2"></i> تقارير التوظيف
    </a>
</div>

{{-- KPI Cards --}}
<div class="row g-4 mb-5">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body text-center py-4">
                <div class="display-6 mb-2">🏢</div>
                <h2 class="fw-bold text-warning mb-1">{{ $pendingEmployers }}</h2>
                <p class="text-muted small mb-3">جهات توظيف قيد الموافقة</p>
                <a href="{{ route('admin.employers.index') }}?status=pending" class="btn btn-sm btn-warning rounded-pill px-3">مراجعة</a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body text-center py-4">
                <div class="display-6 mb-2">💼</div>
                <h2 class="fw-bold text-info mb-1">{{ $pendingJobs }}</h2>
                <p class="text-muted small mb-3">وظائف قيد الموافقة</p>
                <a href="{{ route('admin.employment.jobs.index') }}?status=pending" class="btn btn-sm btn-info text-white rounded-pill px-3">مراجعة</a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body text-center py-4">
                <div class="display-6 mb-2">📋</div>
                <h2 class="fw-bold text-primary mb-1">{{ $newApplications }}</h2>
                <p class="text-muted small mb-3">طلبات توظيف جديدة</p>
                <a href="{{ route('admin.employment.applications.index') }}?status=new" class="btn btn-sm btn-primary rounded-pill px-3">عرض</a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body text-center py-4">
                <div class="display-6 mb-2">✅</div>
                <h2 class="fw-bold text-success mb-1">{{ $hiredThisMonth }}</h2>
                <p class="text-muted small mb-3">توظيف هذا الشهر</p>
                <a href="{{ route('admin.employment.analytics') }}" class="btn btn-sm btn-success rounded-pill px-3">التحليلات</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Pending Employers --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">جهات التوظيف الجديدة</h5>
                <a href="{{ route('admin.employers.index') }}?status=pending" class="small text-primary text-decoration-none">عرض الكل</a>
            </div>
            <div class="card-body px-4">
                @forelse($recentPendingEmployers as $employer)
                    <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                        <div>
                            <div class="fw-semibold">{{ $employer->company_name }}</div>
                            <div class="small text-muted">{{ $employer->user->name }} · {{ $employer->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="d-flex gap-2">
                            <form method="POST" action="{{ route('admin.employers.approve', $employer->user_id) }}">@csrf
                                <button class="btn btn-sm btn-success rounded-pill px-3">قبول</button>
                            </form>
                            <a href="{{ route('admin.employers.show', $employer->user_id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">عرض</a>
                        </div>
                    </div>
                @empty
                    <p class="text-muted py-3 text-center mb-0">لا توجد طلبات معلقة.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Pending Jobs --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">وظائف تنتظر الموافقة</h5>
                <a href="{{ route('admin.employment.jobs.index') }}?status=pending" class="small text-primary text-decoration-none">عرض الكل</a>
            </div>
            <div class="card-body px-4">
                @forelse($recentPendingJobs as $job)
                    <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                        <div>
                            <div class="fw-semibold">{{ $job->title }}</div>
                            <div class="small text-muted">{{ $job->company->company_name ?? '—' }} · {{ $job->created_at->diffForHumans() }}</div>
                        </div>
                        <form method="POST" action="{{ route('admin.employment.jobs.approve', $job) }}">@csrf
                            <button class="btn btn-sm btn-success rounded-pill px-3">نشر</button>
                        </form>
                    </div>
                @empty
                    <p class="text-muted py-3 text-center mb-0">لا توجد وظائف معلقة.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
