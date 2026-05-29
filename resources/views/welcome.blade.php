@extends('layouts.app')

@section('title', __('app.home'))

@section('content')
<!-- Premium Hero Section -->
<div class="row align-items-center min-vh-75 py-5 mb-5 position-relative overflow-hidden hero-section">
    <!-- Decorative visual circles -->
    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10 pointer-events-none" style="background-image: radial-gradient(circle at 20% 30%, #b89047 1px, transparent 1px), radial-gradient(circle at 80% 70%, #b89047 1px, transparent 1px); background-size: 20px 20px;"></div>
    
    <div class="col-lg-7 px-4 px-md-5 py-4 z-1">
        <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill mb-3" style="letter-spacing: 0.5px; font-size: 0.82rem;">
            {{ app()->getLocale() == 'ar' ? 'المنصة الرسمية المعتمدة' : 'Official Approved Portal' }}
        </span>
        <h1 class="display-4 fw-black hero-title mb-3">
            {{ app()->getLocale() == 'ar' ? 'بوابة خريجي جامعة إقليم سبأ' : 'Saba Region University Graduates Portal' }}
        </h1>
        <p class="lead hero-description mb-4 fs-5" style="max-width: 600px;">
            {{ app()->getLocale() == 'ar' 
                ? 'مرحباً بكم في البوابة الأكاديمية والمهنية لخريجي جامعة إقليم سبأ. منصتكم المتكاملة لاستخراج الوثائق الرقمية المعتمدة، ومتابعة الفرص المهنية والتدريبية.' 
                : 'Welcome to the official academic and career portal for Saba Region University graduates. Your integrated space for requesting digital documents, tracking job openings, and connecting.' }}
        </p>
        <div class="d-flex flex-wrap gap-3 mt-4">
            @guest
                <a href="{{ route('register') }}" class="btn btn-gradient btn-lg px-4 py-3 rounded-pill shadow-lg d-flex align-items-center gap-2">
                    <i class="fas fa-user-plus"></i> {{ __('app.register') }}
                </a>
                <a href="#verify-widget" class="btn btn-outline-primary btn-lg px-4 py-3 rounded-pill d-flex align-items-center gap-2">
                    <i class="fas fa-shield-alt"></i> {{ __('app.verify_doc') }}
                </a>
            @else
                <a href="{{ route(Auth::user()->role . '.dashboard') }}" class="btn btn-gradient btn-lg px-5 py-3 rounded-pill shadow-lg d-flex align-items-center gap-2">
                    <i class="fas fa-tachometer-alt"></i> {{ __('app.dashboard') }}
                </a>
            @endguest
        </div>
    </div>
    
    <div class="col-lg-5 text-center py-4 d-none d-lg-block z-1">
        <div class="position-relative d-inline-block">
            <!-- Decorative outer ring -->
            <div class="position-absolute top-50 start-50 translate-middle rounded-circle border border-warning border-opacity-25" style="width: 320px; height: 320px; animation: spin-circle 15s linear infinite;"></div>
            <!-- Main crest background cap -->
            <div class="bg-white bg-opacity-10 p-4 rounded-circle d-inline-block border border-light border-opacity-10 shadow-lg" style="backdrop-filter: blur(12px); width: 260px; height: 260px; display: flex; align-items: center; justify-content: center;">
                <img src="{{ asset('assets/images/university-logo.gif') }}" alt="Saba Region University Logo" style="height: 150px; width: auto; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.2));">
            </div>
            <!-- Floating badge -->
            <div class="position-absolute bottom-0 start-0 bg-white text-dark p-3 rounded-4 shadow-lg text-start d-flex align-items-center gap-3" style="transform: rotate(-5deg); border-left: 4px solid #b89047;">
                <i class="fas fa-award text-warning fa-2x"></i>
                <div>
                    <h6 class="fw-bold mb-0" style="font-size: 0.85rem;">{{ app()->getLocale() == 'ar' ? 'وثائق معتمدة' : 'Verified Degrees' }}</h6>
                    <small class="text-muted" style="font-size: 0.75rem;">{{ app()->getLocale() == 'ar' ? 'آمنة برمز QR' : 'Secured with QR' }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Portal Services Grid Section -->
<div class="py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">{{ app()->getLocale() == 'ar' ? 'خدمات البوابة الإلكترونية' : 'Portal E-Services' }}</h2>
        <p class="text-muted">{{ app()->getLocale() == 'ar' ? 'كل ما تحتاجه كخريج أو صاحب عمل في منصة رقمية متطورة' : 'Everything you need as a graduate or employer in one modern platform' }}</p>
        <div class="mx-auto" style="width: 60px; height: 4px; background: #b89047; border-radius: 2px;"></div>
    </div>

    <div class="row g-4">
        <!-- Document request service -->
        <div class="col-md-4">
            <div class="card h-100 p-4 border-0 border-top border-warning border-4 shadow-sm text-center">
                <div class="icon-box bg-warning bg-opacity-10 text-warning mx-auto">
                    <i class="fas fa-file-invoice fa-2x"></i>
                </div>
                <h4 class="fw-bold mt-3">{{ __('app.documents') }}</h4>
                <p class="text-secondary small px-2">
                    {{ app()->getLocale() == 'ar' 
                        ? 'طلب واستخراج الشهادات الأكاديمية وبيانات الدرجات والوثائق الرسمية إلكترونياً دون عناء.' 
                        : 'Request and issue official academic certificates and transcripts online with ease.' }}
                </p>
                @auth
                    @if(Auth::user()->role === 'graduate')
                        <a href="{{ route('graduate.documents.create') }}" class="btn btn-sm btn-outline-primary mt-auto rounded-pill">{{ __('app.request_document') }}</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary mt-auto rounded-pill">{{ __('app.request_document') }}</a>
                @endauth
            </div>
        </div>

        <!-- Verification service -->
        <div class="col-md-4">
            <div class="card h-100 p-4 border-0 border-top border-primary border-4 shadow-sm text-center">
                <div class="icon-box bg-primary bg-opacity-10 text-primary mx-auto">
                    <i class="fas fa-shield-alt fa-2x"></i>
                </div>
                <h4 class="fw-bold mt-3">{{ __('app.verify_doc') }}</h4>
                <p class="text-secondary small px-2">
                    {{ app()->getLocale() == 'ar' 
                        ? 'نظام ذكي للتحقق الفوري والآمن من مصداقية وصحة الوثائق الصادرة عبر كود التتبع الرقمي.' 
                        : 'Smart, instant and secured system to verify the authenticity of credentials.' }}
                </p>
                <a href="#verify-widget" class="btn btn-sm btn-outline-primary mt-auto rounded-pill">{{ app()->getLocale() == 'ar' ? 'الذهاب للتحقق' : 'Go to Verify' }}</a>
            </div>
        </div>

        <!-- Career board service -->
        <div class="col-md-4">
            <div class="card h-100 p-4 border-0 border-top border-success border-4 shadow-sm text-center">
                <div class="icon-box bg-success bg-opacity-10 text-success mx-auto">
                    <i class="fas fa-briefcase fa-2x"></i>
                </div>
                <h4 class="fw-bold mt-3">{{ __('app.jobs') }}</h4>
                <p class="text-secondary small px-2">
                    {{ app()->getLocale() == 'ar' 
                        ? 'ربط مباشر وموثوق بين خريجي الجامعة والشركات للحصول على أفضل فرص التوظيف والتدريب.' 
                        : 'Direct and reliable connection between graduates and top companies for jobs.' }}
                </p>
                @auth
                    @if(Auth::user()->role === 'graduate')
                        <a href="{{ route('graduate.jobs.index') }}" class="btn btn-sm btn-outline-primary mt-auto rounded-pill">{{ __('app.browse_jobs') }}</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary mt-auto rounded-pill">{{ __('app.browse_jobs') }}</a>
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Interactive Statistics Section -->
<div class="py-5 my-4 bg-light rounded-4 border border-light shadow-sm">
    <div class="container text-center">
        <h3 class="fw-bold mb-2">{{ app()->getLocale() == 'ar' ? 'البوابة بالأرقام' : 'Portal Statistics' }}</h3>
        <p class="text-muted mb-5">{{ app()->getLocale() == 'ar' ? 'أرقام وحقائق تعكس مسيرة نجاح خريجينا الأكاديمية والمهنية' : 'Facts and figures reflecting the academic and professional success' }}</p>
        
        <div class="row g-4 justify-content-center">
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <h2 class="fw-extrabold text-dark mb-1">5,400+</h2>
                    <span class="text-muted small fw-bold">{{ __('app.total_graduates') }}</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success mx-auto mb-3">
                        <i class="fas fa-award fa-lg"></i>
                    </div>
                    <h2 class="fw-extrabold text-dark mb-1">12,600+</h2>
                    <span class="text-muted small fw-bold">{{ __('app.approved_documents') }}</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning mx-auto mb-3">
                        <i class="fas fa-building fa-lg"></i>
                    </div>
                    <h2 class="fw-extrabold text-dark mb-1">320+</h2>
                    <span class="text-muted small fw-bold">{{ app()->getLocale() == 'ar' ? 'أصحاب العمل الشركاء' : 'Partner Employers' }}</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <div class="icon-box bg-info bg-opacity-10 text-info mx-auto mb-3">
                        <i class="fas fa-briefcase fa-lg"></i>
                    </div>
                    <h2 class="fw-extrabold text-dark mb-1">850+</h2>
                    <span class="text-muted small fw-bold">{{ __('app.total_jobs') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Instant Document Verification Widget -->
<div id="verify-widget" class="py-5">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
        <div class="row g-0">
            <!-- Information sidebar -->
            <div class="col-lg-5 bg-gradient text-white p-4 p-md-5 d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, #0b2545 0%, #134074 100%); border-right: 4px solid #b89047;">
                <div>
                    <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-3" style="font-size: 0.75rem;">
                        {{ app()->getLocale() == 'ar' ? 'التحقق السريع' : 'Fast Checking' }}
                    </span>
                    <h3 class="fw-bold mb-3">{{ app()->getLocale() == 'ar' ? 'التحقق الرقمي الفوري' : 'Instant Verification' }}</h3>
                    <p class="small opacity-85 leading-relaxed">
                        {{ app()->getLocale() == 'ar' 
                            ? 'يمكن للشركات والجهات الحكومية التحقق الفوري من صحة وصلاحية وثائق تخرج الطلاب الصادرة عن الجامعة عن طريق كود التتبع التسلسلي المطبوع في أسفل الوثيقة.' 
                            : 'Employers and authorities can instantly verify the authenticity of student credentials using the unique serial number or tracking code.' }}
                    </p>
                </div>
                <div class="pt-4 border-top border-light border-opacity-10 mt-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-qrcode fa-3x text-warning"></i>
                        <div>
                            <h6 class="fw-bold mb-1" style="font-size: 0.85rem;">{{ app()->getLocale() == 'ar' ? 'فحص الباركود الذكي' : 'Barcode & QR Checked' }}</h6>
                            <small class="opacity-75" style="font-size: 0.75rem;">{{ app()->getLocale() == 'ar' ? 'يدعم قراءة أكواد QR المطبوعة' : 'Supports QR printed codes' }}</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Checking form -->
            <div class="col-lg-7 p-4 p-md-5 d-flex align-items-center">
                <div class="w-100">
                    <h4 class="fw-bold text-dark mb-2">{{ app()->getLocale() == 'ar' ? 'تحقق من صحة شهادة الطالب' : 'Verify Student Certificate' }}</h4>
                    <p class="text-muted small mb-4">{{ app()->getLocale() == 'ar' ? 'أدخل رمز التتبع المطبوع على وثيقة الخريج' : 'Enter the tracking code printed on the graduate document' }}</p>
                    
                    <form action="{{ route('verify.process') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="ds-form-label mb-2">{{ __('app.documents_tracking_code') }}</label>
                            <div class="input-group input-group-lg shadow-sm border rounded-pill overflow-hidden">
                                <span class="input-group-text bg-light border-0 text-muted px-3"><i class="fas fa-search"></i></span>
                                <input type="text" name="token" class="form-control border-0 bg-light fs-6" placeholder="SRU-XXXX-XXXX-XXXX" required>
                                <button type="submit" class="btn btn-primary px-4 rounded-pill m-1">{{ __('app.verify_doc') }}</button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="alert alert-light border-0 rounded-3 p-3 small text-muted d-flex gap-2">
                        <i class="fas fa-info-circle text-primary mt-1"></i>
                        <span>
                            {{ app()->getLocale() == 'ar' 
                                ? 'هذا النظام يضمن الشفافية والنزاهة ويمنع تزوير الشهادات الأكاديمية الصادرة عن الجامعة.' 
                                : 'This system ensures absolute integrity and transparency of academic documents issued.' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Careers and Events Highlights Section -->
<div class="py-5 border-top">
    <div class="row g-4">
        <!-- Jobs Highlights -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold m-0 text-primary d-flex align-items-center gap-2">
                        <i class="fas fa-briefcase text-success"></i> 
                        <span>{{ app()->getLocale() == 'ar' ? 'آخر فرص العمل' : 'Latest Job Opportunities' }}</span>
                    </h4>
                    @auth
                        @if(Auth::user()->role === 'graduate')
                            <a href="{{ route('graduate.jobs.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">{{ __('app.view_all') }}</a>
                        @endif
                    @endauth
                </div>
                
                <div class="d-flex flex-column gap-3">
                    <div class="p-3 bg-light rounded-3 border-start border-success border-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">{{ app()->getLocale() == 'ar' ? 'مهندس تطوير برمجيات ويب (Full Stack)' : 'Full Stack Web Developer' }}</h6>
                            <small class="text-muted"><i class="far fa-building me-1"></i> مجموعة شركات سبأ للتكنولوجيا</small>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 small fw-bold">{{ __('app.remote') }}</span>
                    </div>
                    
                    <div class="p-3 bg-light rounded-3 border-start border-success border-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">{{ app()->getLocale() == 'ar' ? 'أخصائي شبكات ونظم تشغيل' : 'Network & System Specialist' }}</h6>
                            <small class="text-muted"><i class="far fa-building me-1"></i> المؤسسة الوطنية للاتصالات</small>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 small fw-bold">{{ __('app.full_time') }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Events Highlights -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold m-0 text-primary d-flex align-items-center gap-2">
                        <i class="fas fa-calendar-alt text-warning"></i> 
                        <span>{{ __('app.latest_news_events') }}</span>
                    </h4>
                    @auth
                        @if(Auth::user()->role === 'graduate')
                            <a href="{{ route('graduate.events.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">{{ __('app.view_all') }}</a>
                        @endif
                    @endauth
                </div>
                
                <div class="d-flex flex-column gap-3">
                    <div class="p-3 bg-light rounded-3 border-start border-warning border-4">
                        <div class="d-flex justify-content-between mb-1">
                            <h6 class="fw-bold text-dark mb-0">{{ __('app.annual_graduation_ceremony') }}</h6>
                            <small class="text-warning fw-bold"><i class="far fa-calendar-alt me-1"></i> 2026-06-15</small>
                        </div>
                        <p class="small text-muted mb-0">{{ __('app.attendance_deadline_desc') }}</p>
                    </div>
                    
                    <div class="p-3 bg-light rounded-3 border-start border-warning border-4">
                        <div class="d-flex justify-content-between mb-1">
                            <h6 class="fw-bold text-dark mb-0">{{ __('app.open_employment_day') }}</h6>
                            <small class="text-warning fw-bold"><i class="far fa-calendar-alt me-1"></i> 2026-07-02</small>
                        </div>
                        <p class="small text-muted mb-0">{{ __('app.employment_day_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes spin-circle {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}
</style>
@endsection
