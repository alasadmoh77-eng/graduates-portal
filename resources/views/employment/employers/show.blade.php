@extends('layouts.app')
@section('title', 'تفاصيل جهة التوظيف: ' . $employerProfile->company_name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.employers.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill mb-3">
        <i class="fas fa-arrow-right me-1"></i> رجوع
    </a>
    <h1 class="h3 fw-bold mb-1">{{ $employerProfile->company_name }}</h1>
    <span class="badge bg-{{ $employerProfile->statusBadge() }} rounded-pill px-3">{{ $employerProfile->statusLabel() }}</span>
</div>

<div class="row g-4">
    {{-- Profile Card --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-3">بيانات الشركة</h5>
            <ul class="list-unstyled d-flex flex-column gap-2 text-muted small">
                <li><i class="fas fa-user me-2 text-primary"></i> {{ $employer->name }}</li>
                <li><i class="fas fa-envelope me-2 text-primary"></i> {{ $employer->email }}</li>
                @if($employerProfile->company_email)
                    <li><i class="fas fa-building me-2 text-primary"></i> {{ $employerProfile->company_email }}</li>
                @endif
                @if($employerProfile->phone)
                    <li><i class="fas fa-phone me-2 text-primary"></i> {{ $employerProfile->phone }}</li>
                @endif
                @if($employerProfile->address)
                    <li><i class="fas fa-map-marker-alt me-2 text-primary"></i> {{ $employerProfile->address }}</li>
                @endif
                @if($employerProfile->website)
                    <li><i class="fas fa-globe me-2 text-primary"></i> <a href="{{ $employerProfile->website }}" target="_blank">{{ $employerProfile->website }}</a></li>
                @endif
                <li><i class="fas fa-calendar me-2 text-primary"></i> {{ $employerProfile->created_at->format('Y/m/d') }}</li>
            </ul>

            @if($employerProfile->rejection_reason)
                <div class="alert alert-danger small mt-3">
                    <strong>سبب الرفض/الإيقاف:</strong> {{ $employerProfile->rejection_reason }}
                </div>
            @endif

            {{-- Actions --}}
            <div class="d-flex flex-column gap-2 mt-3">
                @if(!$employerProfile->isApproved())
                    <form method="POST" action="{{ route('admin.employers.approve', $employer->id) }}">
                        @csrf <button class="btn btn-success rounded-pill">✅ قبول الجهة</button>
                    </form>
                @endif
                @if(!$employerProfile->isRejected())
                    <button class="btn btn-danger rounded-pill" data-bs-toggle="modal" data-bs-target="#rejectModal">❌ رفض</button>
                @endif
                @if($employerProfile->isApproved())
                    <form method="POST" action="{{ route('admin.employers.suspend', $employer->id) }}">
                        @csrf <button class="btn btn-warning rounded-pill">⚠️ إيقاف مؤقت</button>
                    </form>
                @endif
                @if($employerProfile->isSuspended() || $employerProfile->isRejected())
                    <form method="POST" action="{{ route('admin.employers.reactivate', $employer->id) }}">
                        @csrf <button class="btn btn-info text-white rounded-pill">🔄 إعادة تفعيل</button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Application Stats --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mt-4">
            <h6 class="fw-bold mb-3">إحصائيات الطلبات</h6>
            <div class="row g-3 text-center">
                <div class="col-4">
                    <div class="fw-bold fs-4">{{ $applicationStats['total'] }}</div>
                    <div class="small text-muted">إجمالي</div>
                </div>
                <div class="col-4">
                    <div class="fw-bold fs-4 text-info">{{ $applicationStats['shortlisted'] }}</div>
                    <div class="small text-muted">مختصر</div>
                </div>
                <div class="col-4">
                    <div class="fw-bold fs-4 text-success">{{ $applicationStats['hired'] }}</div>
                    <div class="small text-muted">موظّف</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Jobs List --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">الوظائف المنشورة ({{ $jobs->count() }})</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>عنوان الوظيفة</th>
                            <th>الحالة</th>
                            <th>الطلبات</th>
                            <th>تاريخ الانتهاء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $job)
                            <tr>
                                <td class="fw-semibold">{{ $job->title }}</td>
                                <td>
                                    <span class="badge bg-{{ $job->statusBadge() }} rounded-pill px-3">{{ $job->statusLabel() }}</span>
                                </td>
                                <td>{{ $job->applications_count }}</td>
                                <td>{{ $job->deadline->format('Y/m/d') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">لا توجد وظائف.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.employers.reject', $employer->id) }}" class="modal-content rounded-4 border-0">
            @csrf
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">رفض الجهة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label fw-semibold">سبب الرفض <span class="text-danger">*</span></label>
                <textarea name="reason" class="form-control rounded-3" rows="3" required placeholder="اذكر سبب الرفض بوضوح..."></textarea>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-danger rounded-pill px-4">تأكيد الرفض</button>
            </div>
        </form>
    </div>
</div>
@endsection
