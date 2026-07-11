@extends('layouts.app')

@section('title', 'رسائل الاتصال | Contact Messages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">رسائل الاتصال</h3>
    <span class="badge bg-primary rounded-pill">{{ $messages->total() }} رسالة</span>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        @forelse($messages as $msg)
            <div class="p-4 border-bottom {{ is_null($msg->read_at) ? 'bg-primary bg-opacity-10' : '' }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="fw-bold mb-1">{{ $msg->subject }}</h6>
                        <p class="small text-muted mb-1">
                            <i class="fas fa-user me-1"></i> {{ $msg->name }}
                            &nbsp;|&nbsp;
                            <i class="fas fa-envelope me-1"></i> {{ $msg->email }}
                        </p>
                        <p class="mb-2">{{ $msg->message }}</p>
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i> {{ $msg->created_at->format('Y-m-d H:i') }}
                        </small>
                    </div>
                    <div class="text-end">
                        @if(is_null($msg->read_at))
                            <form action="{{ route('admin.contact-messages.read', $msg) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="fas fa-check me-1"></i> تحديد كمقروء
                                </button>
                            </form>
                        @else
                            <span class="badge bg-secondary rounded-pill">مقروءة</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-5 text-center text-muted">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p class="mb-0">لا توجد رسائل.</p>
            </div>
        @endforelse
    </div>
</div>

@if($messages->hasPages())
    <div class="mt-4">
        {{ $messages->links() }}
    </div>
@endif
@endsection
