@extends('layouts.app')

@section('title', 'إدارة الوظائف المعلنة')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-primary">إدارة مراجعة الوظائف</h2>
            <p class="text-muted">هنا يمكنك مراجعة الإعلانات الوظيفية والموافقة عليها لتظهر للخريجين.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3">عنوان الوظيفة</th>
                            <th>صاحب العمل</th>
                            <th>الموقع</th>
                            <th>الحالة</th>
                            <th>تاريخ النشر</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $job)
                            <tr>
                                <td class="fw-bold">{{ $job->title }}</td>
                                <td>{{ $job->company?->company_name ?? $job->employer->name }}</td>
                                <td>{{ $job->location }}</td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'active' => 'bg-success',
                                            'pending' => 'bg-warning',
                                            'closed' => 'bg-secondary',
                                        ];
                                        $class = $statusClasses[$job->status] ?? 'bg-info';
                                    @endphp
                                    <span class="badge {{ $class }} rounded-pill px-3 py-2">{{ ucfirst($job->status) }}</span>
                                    @if($job->is_filled)
                                        <span class="badge bg-danger rounded-pill px-3 py-2 ms-1">{{ app()->getLocale() === 'ar' ? 'تم شغلها' : 'Filled' }}</span>
                                    @else
                                        <span class="badge bg-primary rounded-pill px-3 py-2 ms-1">{{ app()->getLocale() === 'ar' ? 'متاحة' : 'Available' }}</span>
                                    @endif
                                </td>
                                <td>{{ $job->created_at->format('Y-m-d') }}</td>
                                <td>
                                    @if($job->status == 'pending')
                                        <form action="{{ route('admin.jobs.moderate', $job->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">موافقة
                                                وتنشيط</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.jobs.moderate', $job->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="closed">
                                        <button type="submit"
                                            class="btn btn-sm btn-outline-danger rounded-pill px-3 ms-1">إغلاق/حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-5 text-muted">لا توجد إعلانات وظيفية حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection