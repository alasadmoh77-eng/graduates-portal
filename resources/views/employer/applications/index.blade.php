@extends('layouts.app')

@section('title', __('app.applications'))

@section('content')
<h2 class="fw-bold mb-4">{{ __('app.applications') }}</h2>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">{{ __('app.graduate_name') }}</th>
                        <th class="px-4 py-3">{{ __('app.job_title') }}</th>
                        <th class="px-4 py-3">{{ __('app.date') }}</th>
                        <th class="px-4 py-3">{{ __('app.status') }}</th>
                        <th class="px-4 py-3">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="fw-bold">{{ $app->graduate->name }}</span><br>
                                <small class="text-muted">{{ $app->graduate->email }}</small>
                            </td>
                            <td class="px-4 py-3">{{ $app->job->title }}</td>
                            <td class="px-4 py-3">{{ $app->created_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-3">
                                <span class="badge bg-{{ $app->status == 'new' ? 'primary' : 'secondary' }}">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('employer.applications.cv', $app->id) }}" class="btn btn-sm btn-outline-primary shadow-sm rounded-pill px-3">
                                    <i class="fas fa-eye me-1"></i> View CV
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                No applications received yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
