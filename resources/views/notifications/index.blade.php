@extends('layouts.app')

@section('title', 'الإشعارات')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center border-bottom">
                <h4 class="fw-bold mb-0 text-primary"><i class="fas fa-bell me-2"></i> الإشعارات</h4>
                <a href="{{ route('notifications.markAllRead') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">تحديد الكل كمقروء</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($notifications as $notification)
                        <div class="list-group-item p-4 border-0 border-bottom {{ $notification->read_at ? 'opacity-75' : 'bg-light border-start border-primary border-4' }}">
                            <div class="d-flex justify-content-between">
                                <h6 class="fw-bold mb-1">
                                    تحديث حالة الطلب: {{ $notification->data['tracking_code'] ?? 'N/A' }}
                                </h6>
                                <span class="text-muted small">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mb-2 text-secondary">
                                تم تغيير حالة المستند ({{ $notification->data['document_type'] ?? '' }}) إلى 
                                <span class="badge bg-primary rounded-pill">{{ $notification->data['new_status'] ?? '' }}</span>
                            </p>
                            @if(!empty($notification->data['note']))
                                <div class="alert alert-light border small mb-2 italic">
                                    "{{ $notification->data['note'] }}"
                                </div>
                            @endif
                            <div class="d-flex gap-2">
                                <a href="{{ $notification->data['link'] ?? '#' }}" class="btn btn-sm btn-primary rounded-pill px-3">عرض التفاصيل</a>
                                @if(!$notification->read_at)
                                    <form action="{{ route('notifications.markRead', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light rounded-pill px-3 border">تحديد كمقروء</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-bell-slash fa-3x mb-3 opacity-25"></i>
                            <p>لا توجد إشعارات حالياً.</p>
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
