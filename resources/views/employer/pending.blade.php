@extends('layouts.app')
@section('title', 'قيد الموافقة | بوابة الخريجين')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-lg rounded-4 text-center py-5 px-4">
            @php $status = session('employer_status', 'pending'); @endphp

            @if(session('success_registration'))
                <div class="alert alert-success text-center rounded-3 p-4 mb-4 shadow-sm">
                    <div class="mb-2"><i class="fas fa-check-circle fa-2x text-success"></i></div>
                    <h5 class="fw-bold mb-2">تم استلام طلب التسجيل بنجاح.</h5>
                    <p class="mb-0 text-muted">سيتم مراجعة بيانات الشركة من قبل مسؤول التوظيف، وسيتم إشعاركم عند اعتماد الحساب.</p>
                </div>
            @endif

            @if($status === 'pending')
                <div class="mb-4">
                    <span class="display-4">⏳</span>
                </div>
                <h2 class="fw-bold mb-3">طلبك قيد المراجعة</h2>
                <p class="text-muted mb-4">
                    شكراً لتسجيلك كجهة توظيف في بوابة خريجي جامعة إقليم سبأ.
                    سيتم مراجعة طلبك من قِبل مسؤول التوظيف وإشعارك بقرار القبول أو الرفض في أقرب وقت.
                </p>
                <div class="alert alert-info text-start rounded-3">
                    <i class="fas fa-info-circle me-2"></i>
                    يمكنك العودة لتسجيل الدخول بعد الحصول على الموافقة. ستصلك رسالة إشعار فور اتخاذ القرار.
                </div>

            @elseif($status === 'rejected')
                <div class="mb-4">
                    <span class="display-4">❌</span>
                </div>
                <h2 class="fw-bold mb-3 text-danger">تم رفض طلبك</h2>
                <p class="text-muted mb-3">
                    عذراً، تم رفض تسجيل شركتك في البوابة.
                </p>
                @if(session('rejection_reason'))
                    <div class="alert alert-danger text-start rounded-3">
                        <strong>سبب الرفض:</strong> {{ session('rejection_reason') }}
                    </div>
                @endif
                <p class="text-muted small">للاستفسار، يرجى التواصل مع إدارة الجامعة.</p>

            @elseif($status === 'suspended')
                <div class="mb-4">
                    <span class="display-4">🚫</span>
                </div>
                <h2 class="fw-bold mb-3 text-warning">الحساب موقوف مؤقتاً</h2>
                <p class="text-muted mb-4">
                    تم إيقاف حساب شركتك مؤقتاً. يرجى التواصل مع إدارة الجامعة لمعرفة التفاصيل.
                </p>
            @endif

            <div class="d-flex justify-content-center gap-3 mt-3">
                <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="fas fa-sign-in-alt me-1"></i> تسجيل الدخول
                </a>
                <a href="/" class="btn btn-light rounded-pill px-4">
                    <i class="fas fa-home me-1"></i> الرئيسية
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
