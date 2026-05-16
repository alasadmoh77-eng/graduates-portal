@extends('layouts.app')

@section('title', __('app.event_registrations'))

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">{{ __('app.events_trainings') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('app.event_registrations') }}</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-primary mb-1">{{ __('app.event_registrations') }}</h2>
        <p class="text-muted mb-0">
            <span class="fw-bold">{{ $event->title_ar }}</span>
            — {{ $event->start_at->format('Y-m-d H:i') }}
            <span class="badge bg-primary bg-opacity-10 text-primary ms-2">{{ __('app.registrations_total', ['count' => $registrations->total()]) }}</span>
        </p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3">#</th>
                        <th>{{ __('app.name') }}</th>
                        <th>{{ __('app.email') }}</th>
                        <th>{{ __('app.university_id') }}</th>
                        <th>{{ __('app.major') }}</th>
                        <th>{{ __('app.status') }}</th>
                        <th>{{ __('app.registered_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrations as $reg)
                        <tr>
                            <td class="text-muted">{{ $reg->id }}</td>
                            <td class="fw-bold text-start">{{ $reg->graduate->name }}</td>
                            <td class="text-start small">{{ $reg->graduate->email }}</td>
                            <td>{{ $reg->graduate->graduate?->university_id ?? '—' }}</td>
                            <td>{{ $reg->graduate->graduate?->major?->name_ar ?? '—' }}</td>
                            <td><span class="badge bg-secondary rounded-pill">{{ $reg->status }}</span></td>
                            <td class="small text-muted">{{ $reg->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-5 text-muted">لا يوجد مسجلون بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($registrations->hasPages())
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
            {{ $registrations->links() }}
        </div>
    @endif
</div>

<div class="mt-3">
    <a href="{{ route('admin.events.index') }}" class="btn btn-outline-primary rounded-pill px-4">← {{ __('app.events_trainings') }}</a>
    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-light rounded-pill px-4">{{ __('app.edit_event') }}</a>
</div>
@endsection
