@extends('layouts.app')
@section('title', 'إدارة جهات التوظيف')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">إدارة جهات التوظيف</h1>
</div>

{{-- Status Filter Tabs --}}
<div class="d-flex gap-2 flex-wrap mb-4">
    @foreach(['all' => 'الكل', 'pending' => 'قيد المراجعة', 'approved' => 'مقبول', 'rejected' => 'مرفوض', 'suspended' => 'موقوف'] as $key => $label)
        <a href="{{ route('admin.employers.index') }}?status={{ $key }}"
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
                    <th>الشركة</th>
                    <th>المسجل</th>
                    <th>البريد</th>
                    <th>تاريخ التسجيل</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employers as $employer)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $employer->company_name }}</div>
                            @if($employer->website)
                                <div class="small text-muted">{{ $employer->website }}</div>
                            @endif
                        </td>
                        <td>{{ $employer->user->name ?? '—' }}</td>
                        <td>{{ $employer->company_email ?? $employer->user->email ?? '—' }}</td>
                        <td>{{ $employer->created_at->format('Y/m/d') }}</td>
                        <td>
                            <span class="badge bg-{{ $employer->statusBadge() }} rounded-pill px-3">
                                {{ $employer->statusLabel() }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.employers.show', $employer->user_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-eye"></i> عرض
                                </a>
                                @if(!$employer->isApproved())
                                    <form method="POST" action="{{ route('admin.employers.approve', $employer->user_id) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success rounded-pill px-3">قبول</button>
                                    </form>
                                @endif
                                @if(!$employer->isRejected())
                                    <button class="btn btn-sm btn-danger rounded-pill px-3"
                                        data-bs-toggle="modal" data-bs-target="#rejectModal{{ $employer->user_id }}">رفض</button>
                                @endif
                                @if($employer->isApproved())
                                    <form method="POST" action="{{ route('admin.employers.suspend', $employer->user_id) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-warning rounded-pill px-3">إيقاف</button>
                                    </form>
                                @endif
                                @if($employer->isSuspended() || $employer->isRejected())
                                    <form method="POST" action="{{ route('admin.employers.reactivate', $employer->user_id) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-info text-white rounded-pill px-3">إعادة تفعيل</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>

                    {{-- Reject Modal --}}
                    <div class="modal fade" id="rejectModal{{ $employer->user_id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('admin.employers.reject', $employer->user_id) }}" class="modal-content rounded-4 border-0">
                                @csrf
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold">رفض جهة التوظيف</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted">الشركة: <strong>{{ $employer->company_name }}</strong></p>
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
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">لا توجد نتائج.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($employers->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $employers->links() }}
        </div>
    @endif
</div>
@endsection
