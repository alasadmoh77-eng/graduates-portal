@extends('layouts.app')

@section('title', 'طلبات التوظيف المقدمة | بوابة الخريجين')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1 text-primary">طلبات التوظيف المقدمة</h1>
            <p class="text-muted mb-0">تابع حالة طلبات التقديم لفرص العمل والشركات في الوقت الحقيقي</p>
        </div>
        <a href="{{ route('graduate.jobs.index') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-briefcase me-1"></i> تصفح الوظائف المتاحة
        </a>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3 h-100">
                <h6 class="text-muted small mb-2">إجمالي الطلبات</h6>
                <div class="fs-3 fw-bold text-dark">{{ $counts['total'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3 h-100 border-bottom border-secondary border-3">
                <h6 class="text-muted small mb-2">مقدمة حديثاً</h6>
                <div class="fs-3 fw-bold text-secondary">{{ $counts['new'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3 h-100 border-bottom border-info border-3">
                <h6 class="text-muted small mb-2">مختصرة (Shortlisted)</h6>
                <div class="fs-3 fw-bold text-info">{{ $counts['shortlisted'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3 h-100 border-bottom border-primary border-3">
                <h6 class="text-muted small mb-2">مقابلات مجدولة</h6>
                <div class="fs-3 fw-bold text-primary">{{ $counts['interviewed'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3 h-100 border-bottom border-success border-3">
                <h6 class="text-muted small mb-2">مقبولة / توظيف</h6>
                <div class="fs-3 fw-bold text-success">{{ $counts['hired'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3 h-100 border-bottom border-danger border-3">
                <h6 class="text-muted small mb-2">مرفوضة</h6>
                <div class="fs-3 fw-bold text-danger">{{ $counts['rejected'] }}</div>
            </div>
        </div>
    </div>

    <!-- Applications List -->
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary border-bottom">
                        <tr>
                            <th class="px-4 py-3">الوظيفة والشركة</th>
                            <th class="px-4 py-3">الموقع والنوع</th>
                            <th class="px-4 py-3">تاريخ التقديم</th>
                            <th class="px-4 py-3">حالة الطلب</th>
                            <th class="px-4 py-3 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2.5 d-flex justify-content-center align-items-center" style="width: 45px; height: 45px;">
                                            <i class="fas fa-building fs-5"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold d-block text-dark">{{ $app->job->title }}</span>
                                            <small class="text-muted">{{ $app->job->company->company_name ?? 'شركة غير معروفة' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-light text-secondary px-3 py-1.5 rounded-pill mb-1 d-inline-block">{{ $app->job->location }}</span>
                                    <small class="text-muted d-block">{{ $app->job->job_type }}</small>
                                </td>
                                <td class="px-4 py-3 text-muted">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    {{ $app->created_at->format('Y-m-d') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-{{ $app->statusBadge() }} px-3 py-2 rounded-pill text-white fw-normal">
                                        {{ $app->statusLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('graduate.applications.show', $app->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-4 shadow-sm">
                                        <i class="fas fa-info-circle me-1"></i> تفاصيل وتتبع
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="py-5">
                                        <i class="fas fa-briefcase fa-3x mb-3 text-secondary"></i>
                                        <p class="fs-5 fw-bold mb-1">لم تقم بالتقديم على أي وظائف بعد</p>
                                        <p class="text-muted small">تصفح لوحة فرص العمل المتاحة وقدم طلباتك للشركات لتبدأ مسيرتك المهنية.</p>
                                        <a href="{{ route('graduate.jobs.index') }}" class="btn btn-primary rounded-pill px-4 mt-2">تصفح الوظائف الآن</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Pagination -->
    <div class="mt-3">
        {{ $applications->links() }}
    </div>
</div>
@endsection
