@extends('layouts.app')

@section('title', __('app.notifications'))

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center border-bottom">
                <h4 class="fw-bold mb-0 text-primary"><i class="fas fa-bell me-2"></i> {{ __('app.notifications') }}</h4>
                <a href="{{ route('notifications.markAllRead') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">{{ __('app.mark_all_read') }}</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($notifications as $notification)
                        <div class="list-group-item p-4 border-0 border-bottom {{ $notification->read_at ? 'opacity-75' : 'bg-light border-start border-primary border-4' }}">
                            <div class="d-flex justify-content-between">
                                <h6 class="fw-bold mb-1">
                                    @if(isset($notification->data['type']) && $notification->data['type'] === 'new_employer_registered')
                                        {{ app()->getLocale() == 'ar' ? 'طلب تسجيل جهة توظيف جديدة قيد المراجعة' : 'New employer registration request awaiting review.' }}
                                    @elseif(($notification->data['type'] ?? '') === 'signature_required')
                                        {{ $notification->data['title'] ?? 'توقيع مطلوب' }}
                                    @elseif(in_array(($notification->data['type'] ?? ''), ['payment_proof_review', 'new_payment_proof_submitted']))
                                        {{ $notification->data['title'] ?? 'طلب دفع جديد قيد المراجعة' }}
                                    @elseif(($notification->data['type'] ?? '') === 'new_graduate_registered')
                                        {{ $notification->data['title'] ?? 'تسجيل خريج جديد' }}
                                    @elseif(isset($notification->data['tracking_code']))
                                        {{ __('app.status_update') }} {{ $notification->data['tracking_code'] }}
                                    @else
                                        {{ $notification->data['message'] ?? __('app.new_notification') }}
                                    @endif
                                </h6>
                                <span class="text-muted small">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                            @if(isset($notification->data['type']) && $notification->data['type'] === 'new_graduate_registered')
                                <p class="mb-2 text-secondary">
                                    <i class="fas fa-user-graduate text-primary me-1"></i>
                                    {{ $notification->data['graduate_name'] ?? 'غير محدد' }}
                                </p>
                                <p class="mb-2 small text-muted">
                                    {{ $notification->data['graduate_email'] ?? 'غير محدد' }} &middot; {{ __('app.registration_date') }}: {{ $notification->data['registration_date'] ?? 'غير محدد' }}
                                </p>
                            @elseif(($notification->data['type'] ?? '') === 'signature_required')
                                <p class="mb-2 text-secondary">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                            @elseif(isset($notification->data['type']) && $notification->data['type'] === 'new_employer_registered')
                                <p class="mb-2 text-secondary">
                                    <i class="fas fa-building text-primary me-1"></i>
                                    <strong>{{ app()->getLocale() == 'ar' ? 'اسم الشركة' : 'Company Name' }}:</strong> {{ $notification->data['company_name'] ?? 'غير محدد' }}
                                </p>
                                <p class="mb-2 small text-muted">
                                    {{ app()->getLocale() == 'ar' ? 'تاريخ التسجيل' : 'Registration Date' }}: {{ $notification->data['registration_date'] ?? 'غير محدد' }}
                                </p>
                            @elseif(isset($notification->data['tracking_code']))
                                <p class="mb-2 text-secondary">
                                    @if(!empty($notification->data['old_status']))
                                        {{ __('app.status_from') }}
                                        <span class="badge bg-secondary rounded-pill">{{ __('app.document_status.'.$notification->data['old_status']) }}</span>
                                    @endif
                                    @if(!empty($notification->data['new_status']))
                                        {{ __('app.status_to') }}
                                        <span class="badge bg-primary rounded-pill">{{ __('app.document_status.'.$notification->data['new_status']) }}</span>
                                    @endif
                                </p>
                                @if(!empty($notification->data['note']))
                                    <div class="alert alert-light border small mb-2">
                                        "{{ $notification->data['note'] }}"
                                    </div>
                                @endif
                            @else
                                <p class="mb-2 text-secondary">{{ $notification->data['message'] ?? '' }}</p>
                            @endif
                            <div class="d-flex gap-2">
                                <a href="{{ $notification->data['link'] ?? '#' }}" class="btn btn-sm btn-primary rounded-pill px-3">{{ __('app.view_details') }}</a>
                                @if(!$notification->read_at)
                                    <form action="{{ route('notifications.markRead', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light rounded-pill px-3 border">{{ __('app.mark_read') }}</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-bell-slash fa-3x mb-3 opacity-25"></i>
                            <p>{{ __('app.no_notifications') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
            @if($notifications->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
