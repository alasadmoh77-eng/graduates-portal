@extends('layouts.app')

@section('title', __('app.home'))

@section('content')
<div class="row align-items-center min-vh-75">
    <div class="col-lg-6 mb-5 mb-lg-0">
        <h1 class="display-3 fw-bold text-primary mb-4">
            {{ app()->getLocale() == 'ar' ? 'مستقبلك يبدأ من هنا' : 'Your Future Starts Here' }}
        </h1>
        <p class="lead text-secondary mb-5">
            {{ app()->getLocale() == 'ar' 
                ? 'البوابة الإلكترونية الشاملة لخريجي جامعة إقليم سبأ. اطلب وثائقك، تابع مسارك المهني، وتواصل مع أصحاب العمل في منصة واحدة.' 
                : 'The comprehensive electronic portal for Sabaa Region University graduates. Request documents, track your career path, and connect with employers in one platform.' }}
        </p>
        <div class="d-flex gap-3">
            @guest
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-lg">
                    {{ __('app.register') }}
                </a>
                <a href="{{ route('verify.show') }}" class="btn btn-outline-primary btn-lg px-5 py-3 rounded-pill">
                    {{ __('app.verify_doc') }}
                </a>
            @else
                <a href="{{ route(Auth::user()->role . '.dashboard') }}" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-lg">
                    {{ __('app.dashboard') }}
                </a>
            @endguest
        </div>
    </div>
    <div class="col-lg-6 text-center">
        <!-- Placeholder for hero image -->
        <div class="bg-primary bg-opacity-10 p-5 rounded-circle d-inline-block shadow-sm">
            <i class="fas fa-graduation-cap text-primary" style="font-size: 15rem;"></i>
        </div>
    </div>
</div>

<div class="row mt-5 pt-5 text-center">
    <div class="col-md-4 mb-4">
        <div class="card p-4 h-100 border-top border-primary border-4">
            <div class="mb-3">
                <i class="fas fa-file-invoice fa-3x text-primary opacity-75"></i>
            </div>
            <h4 class="fw-bold">{{ __('app.documents') }}</h4>
            <p class="text-secondary">
                {{ app()->getLocale() == 'ar' ? 'طلب واستخراج الشهادات وبيانات الدرجات إلكترونياً وبكل سهولة.' : 'Request and issue certificates and transcripts online with ease.' }}
            </p>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card p-4 h-100 border-top border-success border-4">
            <div class="mb-3">
                <i class="fas fa-briefcase fa-3x text-success opacity-75"></i>
            </div>
            <h4 class="fw-bold">{{ __('app.jobs') }}</h4>
            <p class="text-secondary">
                {{ app()->getLocale() == 'ar' ? 'فرص وظيفية حصرية لخريجي الجامعة في مختلف التخصصات.' : 'Exclusive job opportunities for university graduates across various fields.' }}
            </p>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card p-4 h-100 border-top border-warning border-4">
            <div class="mb-3">
                <i class="fas fa-shield-alt fa-3x text-warning opacity-75"></i>
            </div>
            <h4 class="fw-bold">{{ __('app.verify_doc') }}</h4>
            <p class="text-secondary">
                {{ app()->getLocale() == 'ar' ? 'نظام تحقق سريع وموثوق عبر رمز الاستجابة السريع (QR Code).' : 'Fast and reliable verification system via QR Code.' }}
            </p>
        </div>
    </div>
</div>
@endsection
