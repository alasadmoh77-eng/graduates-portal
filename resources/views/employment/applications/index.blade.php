@extends('layouts.app')
@section('title', 'طلبات التوظيف — نظرة عامة')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">طلبات التوظيف</h1>
</div>

{{-- Status Filter Tabs --}}
<div class="d-flex gap-2 flex-wrap mb-4">
    @foreach(['all' => 'الكل', 'new' => 'جديد', 'shortlisted' => 'مختصر', 'interviewed' => 'مقابلة', 'hired' => 'موظّف', 'rejected' => 'مرفوض'] as $key => $label)
        <a href="{{ route('admin.employment.applications.index') }}?status={{ $key }}"
           class="btn rounded-pill px-3 {{ $status === $key ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $label }} <span class="badge bg-white text-dark ms-1">{{ $counts[$key] }}</span>
        </a>
    @endforeach
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>المتقدم</th>
                    <th>الوظيفة</th>
                    <th>الشركة</th>
                    <th>الحالة</th>
                    <th>تاريخ التقديم</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td>{{ $app->graduate->name ?? '—' }}</td>
                        <td class="fw-semibold">{{ $app->job->title ?? '—' }}</td>
                        <td>{{ $app->job->company->company_name ?? '—' }}</td>
                        <td>
                            <span class="badge bg-{{ $app->statusBadge() }} rounded-pill px-3">{{ $app->statusLabel() }}</span>
                        </td>
                        <td>{{ $app->created_at->format('Y/m/d') }}</td>
                        <td>
                            <a href="{{ route('admin.employment.applications.show', $app) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="fas fa-eye"></i> تفاصيل
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">لا توجد طلبات.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($applications->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $applications->links() }}</div>
    @endif
</div>
@endsection
