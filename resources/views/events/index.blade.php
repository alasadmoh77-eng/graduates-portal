@extends('layouts.app')

@section('title', __('app.events'))

@section('content')
<div class="mb-4">
    <x-page-header 
        :title="__('app.events')"
        :subtitle="__('app.events_subtitle')"
        icon="fa-calendar-alt"
    />
</div>

<div class="row">
    @forelse($events as $event)
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-warning bg-opacity-10 text-dark px-3 py-1 rounded-pill">
                            <i class="fas fa-users me-1"></i> {{ $event->seats }} {{ __('app.seats') }}
                        </span>
                        <small class="text-muted"><i class="fas fa-calendar me-1"></i> {{ $event->start_at->format('M d, Y') }}</small>
                    </div>
                    
                    <h5 class="fw-bold mb-3">
                        {{ app()->getLocale() == 'ar' ? $event->title_ar : $event->title_en }}
                    </h5>
                    
                    <p class="text-muted small mb-4">
                        {{ app()->getLocale() == 'ar' ? Str::limit($event->description_ar, 100) : Str::limit($event->description_en, 100) }}
                    </p>
                    
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2 small text-muted">
                            <i class="fas fa-clock me-2"></i> {{ $event->start_at->format('h:i A') }}
                        </div>
                        <div class="d-flex align-items-center small text-muted">
                            <i class="fas fa-map-marker-alt me-2"></i> {{ $event->location }}
                        </div>
                    </div>
                    
                    <form action="{{ route('graduate.events.register', $event->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100 rounded-pill fw-bold">
                            {{ __('app.register_event') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <x-empty-state
                icon="fa-calendar-alt"
                :title="__('app.no_events')"
            />
        </div>
    @endforelse
</div>
@endsection
