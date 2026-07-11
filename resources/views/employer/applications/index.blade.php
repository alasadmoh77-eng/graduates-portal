@extends('layouts.app')

@section('title', __('app.applications'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1 text-primary">{{ __('app.applications') }}</h1>
        <p class="text-muted mb-0">قائمة بجميع الخريجين المتقدمين للوظائف المعلنة من قبل شركتكم</p>
    </div>
</div>

<div class="card border-0 shadow-lg rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary border-bottom">
                    <tr>
                        <th class="px-4 py-3">{{ __('app.graduate_name') }}</th>
                        <th class="px-4 py-3">{{ __('app.job_title') }}</th>
                        <th class="px-4 py-3">{{ __('app.date') }}</th>
                        <th class="px-4 py-3">{{ __('app.status') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex justify-content-center align-items-center" style="width: 45px; height: 45px;">
                                        <i class="fas fa-user-graduate fs-5"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block text-dark">{{ $app->graduate->name }}</span>
                                        <small class="text-muted">{{ $app->graduate->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="fw-semibold text-secondary">{{ $app->job->title }}</span>
                            </td>
                            <td class="px-4 py-3 text-muted">
                                <i class="far fa-calendar-alt me-1"></i>
                                {{ $app->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-{{ $app->statusBadge() }} px-3 py-2 rounded-pill fw-normal text-white">
                                    {{ $app->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('employer.applications.show', $app->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                        <i class="fas fa-search me-1"></i> مراجعة والتحكم
                                    </a>
                                    @if($app->cv_path)
                                        <a href="{{ route('employer.applications.cv', $app->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                            <i class="fas fa-download me-1"></i> CV
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <i class="far fa-folder-open fa-3x mb-3 text-secondary"></i>
                                    <p class="fs-5 fw-bold mb-1">لا توجد طلبات تقديم بعد</p>
                                    <p class="text-muted small">ستظهر طلبات الخريجين هنا بمجرد تقديمهم على إعلاناتك الوظيفية النشطة.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
