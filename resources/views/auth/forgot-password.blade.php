@extends('layouts.app')

@section('title', 'نسيت كلمة المرور؟')

@section('content')
<div class="row justify-content-center align-items-center min-vh-50">
    <div class="col-md-5">
        <div class="card p-4 shadow-lg text-center">
            <div class="mb-4">
                <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                <h2 class="fw-bold">نسيت كلمة المرور؟</h2>
            </div>

            <p class="text-muted mb-4">
                لاستعادة الوصول إلى حسابك وإعادة تعيين كلمة المرور، يرجى التواصل مع المسؤول العام للنظام، فهو الجهة الوحيدة المخولة بتنفيذ ذلك.
            </p>

            <div>
                <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-5 fw-bold">
                    <i class="fas fa-arrow-right me-1"></i> العودة إلى تسجيل الدخول
                </a>
            </div>

            <div class="mt-4 pt-3 border-top">
                <a href="https://wa.me/967780641221" target="_blank" class="text-decoration-none" title="WhatsApp">
                    <i class="fab fa-whatsapp fa-2x text-success"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
