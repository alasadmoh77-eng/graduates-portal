@extends('layouts.app')
@section('title', 'مراجعة الوظائف')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">مراجعة الوظائف</h1>
</div>

{{-- Status Filter Tabs --}}
<div class="d-flex gap-2 flex-wrap mb-4">
    @foreach(['all' => 'الكل', 'pending' => 'قيد المراجعة', 'active' => 'نشط', 'closed' => 'مغلق', 'rejected' => 'مرفوض'] as $key => $label)
        <a href="{{ route('admin.employment.jobs.index') }}?status={{ $key }}"
           class="btn rounded-pill px-4 {{ $status === $key ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $label }}
            <span class="badge bg-white text-dark ms-1">{{ $counts[$key] }}</span>
        </a>
    @endforeach
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>عنوان الوظيفة</th>
                    <th>جهة التوظيف</th>
                    <th>النوع</th>
                    <th>آخر موعد</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $job->title }}</div>
                            <div class="small text-muted">{{ Str::limit($job->description, 60) }}</div>
                        </td>
                        <td>{{ $job->company->company_name ?? $job->employer->name ?? '—' }}</td>
                        <td>{{ $job->job_type ?? '—' }}</td>
                        <td>{{ $job->deadline->format('Y/m/d') }}</td>
                        <td>
                            <span class="badge bg-{{ $job->statusBadge() }} rounded-pill px-3">{{ $job->statusLabel() }}</span>
                            @if($job->is_filled)
                                <span class="badge bg-danger rounded-pill px-3 ms-1">{{ app()->getLocale() === 'ar' ? 'تم شغلها' : 'Filled' }}</span>
                            @else
                                <span class="badge bg-primary rounded-pill px-3 ms-1">{{ app()->getLocale() === 'ar' ? 'متاحة' : 'Available' }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                @if($job->isPending())
                                    <form method="POST" action="{{ route('admin.employment.jobs.approve', $job) }}">@csrf
                                        <button class="btn btn-sm btn-success rounded-pill px-3">نشر</button>
                                    </form>
                                    <button class="btn btn-sm btn-danger rounded-pill px-3"
                                        data-bs-toggle="modal" data-bs-target="#rejectJobModal{{ $job->id }}">رفض</button>
                                @endif
                                @if($job->isActive())
                                    <form method="POST" action="{{ route('admin.employment.jobs.close', $job) }}">@csrf
                                        <button class="btn btn-sm btn-secondary rounded-pill px-3">إغلاق</button>
                                    </form>
                                @endif
                                @if($job->rejection_reason)
                                    <span class="small text-danger" title="{{ $job->rejection_reason }}">
                                        <i class="fas fa-info-circle"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>

                    @if($job->isPending())
                    <div class="modal fade" id="rejectJobModal{{ $job->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('admin.employment.jobs.reject', $job) }}" class="modal-content rounded-4 border-0">
                                @csrf
                                <div class="modal-header border-0">
                                    <h5 class="modal-title fw-bold">رفض الوظيفة</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted">الوظيفة: <strong>{{ $job->title }}</strong></p>
                                    <label class="form-label fw-semibold">سبب الرفض <span class="text-danger">*</span></label>
                                    <textarea name="reason" class="form-control rounded-3" rows="3" required placeholder="اذكر سبب الرفض..."></textarea>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                                    <button type="submit" class="btn btn-danger rounded-pill px-4">تأكيد الرفض</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">لا توجد وظائف.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($jobs->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $jobs->links() }}</div>
    @endif
</div>
@endsection
