@extends('layouts.app')

@section('title', __('app.view_my_listings'))

@section('content')
<x-page-header 
    :title="__('app.view_my_listings')"
    icon="fa-briefcase"
>
    <a href="{{ route('employer.jobs.create') }}" class="btn btn-primary rounded-pill px-4">
        <i class="fas fa-plus me-2"></i> {{ __('app.post_new_job') }}
    </a>
</x-page-header>

<x-section-card noPadding="true">
        <div class="table-responsive">
            <table class="table ds-table mb-0">
                <thead>
                    <tr>
                        <th class="px-4 py-3">{{ __('app.job_title') }}</th>
                        <th class="px-4 py-3">{{ __('app.date') }}</th>
                        <th class="px-4 py-3">{{ __('app.status') }}</th>
                        <th class="px-4 py-3">{{ __('app.applications') }}</th>
                        <th class="px-4 py-3">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="fw-bold">{{ $job->title }}</span><br>
                                <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i> {{ $job->location }}</small>
                            </td>
                            <td class="px-4 py-3">{{ $job->created_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $badgeClass = [
                                        'active' => 'success',
                                        'pending' => 'warning',
                                        'closed' => 'secondary'
                                    ][$job->status] ?? 'info';
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ ucfirst($job->status) }}</span>
                                @if($job->is_filled)
                                    <span class="badge bg-danger ms-1">{{ app()->getLocale() === 'ar' ? 'تم شغلها' : 'Filled' }}</span>
                                @else
                                    <span class="badge bg-primary ms-1">{{ app()->getLocale() === 'ar' ? 'متاحة' : 'Available' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-info bg-opacity-10 text-info px-3">{{ $job->applications_count ?? 0 }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="btn btn-sm btn-outline-secondary btn-disabled-state"
                                      data-bs-toggle="tooltip"
                                      data-bs-placement="top"
                                      title="{{ __('app.coming_soon') }}">
                                    {{ __('app.process') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-0">
                                <x-empty-state
                                    icon="fa-briefcase"
                                    :title="__('app.no_jobs_yet')"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
</x-section-card>
@endsection
