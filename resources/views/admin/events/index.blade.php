@extends('layouts.app')

@section('title', __('app.events_trainings'))

@section('content')
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold text-primary mb-1">{{ __('app.events_trainings') }}</h2>
            <p class="text-muted mb-0">عرض الفعاليات والتدريبات، إدارتها، ومشاهدة المسجلين.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                <i class="fas fa-plus me-1"></i> {{ __('app.new_event') }}
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3">العنوان (عربي)</th>
                            <th>العنوان (إنجليزي)</th>
                            <th>التاريخ والوقت</th>
                            <th>المكان</th>
                            <th>المقاعد</th>
                            <th>المسجلون</th>
                            <th>الحالة</th>
                            <th class="text-nowrap">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                            <tr>
                                <td class="fw-bold text-start">{{ $event->title_ar }}</td>
                                <td class="text-start">{{ $event->title_en }}</td>
                                <td>
                                    <div class="small">{{ $event->start_at->format('Y-m-d') }}</div>
                                    <div class="text-muted small">{{ $event->start_at->format('H:i') }}</div>
                                </td>
                                <td>{{ $event->location ?? '—' }}</td>
                                <td>{{ $event->seats !== null ? $event->seats : '—' }}</td>
                                <td>
                                    <a href="{{ route('admin.events.registrations', $event) }}" class="text-decoration-none">
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                                            {{ $event->registrations_count }}
                                        </span>
                                    </a>
                                </td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'upcoming' => 'bg-success',
                                            'completed' => 'bg-secondary',
                                            'cancelled' => 'bg-danger',
                                        ];
                                        $class = $statusClasses[$event->status] ?? 'bg-info';
                                    @endphp
                                    <span class="badge {{ $class }} rounded-pill px-3 py-2">{{ $event->status }}</span>
                                </td>
                                <td class="text-nowrap">
                                    <div class="btn-group btn-group-sm flex-wrap gap-1" role="group">
                                        <a href="{{ route('admin.events.registrations', $event) }}" class="btn btn-outline-primary rounded-pill px-2" title="{{ __('app.event_registrations') }}">
                                            <i class="fas fa-users"></i>
                                        </a>
                                        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-outline-secondary rounded-pill px-2" title="{{ __('app.edit_event') }}">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        @if($event->status !== 'cancelled')
                                            <form action="{{ route('admin.events.cancel', $event) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm(@json(__('app.event_cancel_confirm')));">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-warning rounded-pill px-2" title="{{ __('app.cancel_event') }}">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm(@json(__('app.confirm_delete_event')));">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger rounded-pill px-2" title="{{ __('app.delete_event') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-5 text-muted">لا توجد فعاليات مسجلة حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
