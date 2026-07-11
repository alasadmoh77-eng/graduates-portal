@extends('layouts.app')

@section('title', __('app.about_alumni'))

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-lg-10">
        <!-- Hero -->
        <div class="text-center mb-5">
            <h1 class="fw-bold" style="color: var(--primary-blue);">
                {{ app()->getLocale() == 'ar' ? 'عن شؤون الخريجين' : 'About Alumni Affairs' }}
            </h1>
            <p class="text-muted fs-6">
                {{ app()->getLocale() == 'ar' ? 'إدارة شؤون الخريجين بجامعة إقليم سبأ' : 'Alumni Affairs Department – Saba Region University' }}
            </p>
        </div>

        <!-- About -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        <div class="bg-primary bg-opacity-10 rounded-4 p-4 d-inline-block">
                            <i class="fas fa-university fa-3x" style="color: var(--primary-blue);"></i>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <h4 class="fw-bold mb-3" style="color: var(--primary-blue);">
                            {{ app()->getLocale() == 'ar' ? 'نبذة عن الإدارة' : 'About the Department' }}
                        </h4>
                        <p class="text-muted leading-relaxed" style="line-height: 1.9;">
                            {{ app()->getLocale() == 'ar'
                                ? 'إدارة شؤون الخريجين بجامعة إقليم سبأ هي الجهة المسؤولة عن متابعة شؤون خريجي الجامعة بعد تخرجهم، وتقديم الخدمات الأكاديمية والإدارية لهم، وتعزيز التواصل بين الخريجين والجامعة، وربط الخريجين بسوق العمل من خلال الشراكات مع القطاعين العام والخاص.'
                                : 'The Alumni Affairs Department at Saba Region University is responsible for following up on graduates\' affairs after graduation, providing academic and administrative services, enhancing communication between graduates and the university, and connecting graduates with the job market through partnerships with public and private sectors.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Vision -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-md-5 text-center">
                        <div class="bg-warning bg-opacity-10 rounded-4 p-3 d-inline-block mb-3">
                            <i class="fas fa-eye fa-2x" style="color: var(--accent-gold);"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--primary-blue);">
                            {{ app()->getLocale() == 'ar' ? 'الرؤية' : 'Vision' }}
                        </h4>
                        <p class="text-muted mb-0">
                            {{ app()->getLocale() == 'ar'
                                ? 'الريادة والتميز في تقديم خدمات مبتكرة للخريجين وتعزيز ارتباطهم بالجامعة ومجتمعهم.'
                                : 'Leadership and excellence in providing innovative services to graduates and strengthening their connection to the university and their community.' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mission -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-md-5 text-center">
                        <div class="bg-success bg-opacity-10 rounded-4 p-3 d-inline-block mb-3">
                            <i class="fas fa-bullseye fa-2x text-success"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--primary-blue);">
                            {{ app()->getLocale() == 'ar' ? 'الرسالة' : 'Mission' }}
                        </h4>
                        <p class="text-muted mb-0">
                            {{ app()->getLocale() == 'ar'
                                ? 'تقديم خدمات متميزة للخريجين من خلال حلول رقمية مبتكرة، وتسهيل حصولهم على الوثائق الرسمية، وتمكينهم من فرص التدريب والتوظيف.'
                                : 'Providing distinguished services to graduates through innovative digital solutions, facilitating access to official documents, and enabling training and employment opportunities.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Objectives -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="bg-info bg-opacity-10 rounded-4 p-3 d-inline-block mb-3">
                        <i class="fas fa-list-check fa-2x text-info"></i>
                    </div>
                    <h4 class="fw-bold" style="color: var(--primary-blue);">
                        {{ app()->getLocale() == 'ar' ? 'الأهداف' : 'Objectives' }}
                    </h4>
                </div>
                <div class="row g-3">
                    @php
                        $objectives = app()->getLocale() == 'ar'
                            ? [
                                'تسهيل حصول الخريجين على الوثائق الرسمية إلكترونياً.',
                                'تعزيز التواصل المستمر بين الخريجين والجامعة.',
                                'ربط الخريجين بفرص التوظيف والتدريب المناسبة.',
                                'تطوير قاعدة بيانات شاملة للخريجين.',
                                'تنظيم الفعاليات والبرامج التدريبية للخريجين.',
                                'التحقق من صحة الوثائق الصادرة إلكترونياً عبر رمز QR.',
                                'تقديم الدعم والإرشاد المهني للخريجين.',
                              ]
                            : [
                                'Facilitate graduates\' electronic access to official documents.',
                                'Enhance continuous communication between graduates and the university.',
                                'Connect graduates with suitable employment and training opportunities.',
                                'Develop a comprehensive alumni database.',
                                'Organize events and training programs for graduates.',
                                'Verify the authenticity of issued documents electronically via QR code.',
                                'Provide career support and guidance for graduates.',
                            ];
                    @endphp
                    @foreach($objectives as $obj)
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background: #f8fafc;">
                                <div class="bg-primary bg-opacity-10 rounded-2 p-2 mt-1">
                                    <i class="fas fa-check text-primary"></i>
                                </div>
                                <span>{{ $obj }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Services -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-4 p-3 d-inline-block mb-3">
                        <i class="fas fa-concierge-bell fa-2x" style="color: var(--primary-blue);"></i>
                    </div>
                    <h4 class="fw-bold" style="color: var(--primary-blue);">
                        {{ app()->getLocale() == 'ar' ? 'الخدمات المقدمة' : 'Services Provided' }}
                    </h4>
                </div>
                <div class="row g-3">
                    @php
                        $services = app()->getLocale() == 'ar'
                            ? [
                                ['icon' => 'fa-file-invoice', 'title' => 'طلب الوثائق الأكاديمية', 'desc' => 'تقديم طلبات الحصول على الوثائق الرسمية إلكترونياً.'],
                                ['icon' => 'fa-book-open', 'title' => 'خدمات السجل الأكاديمي', 'desc' => 'استخراج السجل الأكاديمي الرسمي المعتمد.'],
                                ['icon' => 'fa-file-certificate', 'title' => 'خدمات شهادة الدرجات', 'desc' => 'إصدار شهادات الدرجات والتقديرات المعتمدة.'],
                                ['icon' => 'fa-qrcode', 'title' => 'التحقق من الوثائق بـ QR', 'desc' => 'التحقق الإلكتروني من صحة الوثائق عبر رمز الاستجابة السريعة.'],
                                ['icon' => 'fa-briefcase', 'title' => 'فرص العمل', 'desc' => 'نشر فرص التوظيف والتقديم عليها إلكترونياً.'],
                                ['icon' => 'fa-calendar-alt', 'title' => 'الفعاليات والتدريبات', 'desc' => 'التسجيل في الفعاليات والبرامج التدريبية المتنوعة.'],
                                ['icon' => 'fa-comments', 'title' => 'التواصل مع الخريجين', 'desc' => 'قناة تواصل مباشرة بين الخريجين وإدارة الجامعة.'],
                              ]
                            : [
                                ['icon' => 'fa-file-invoice', 'title' => 'Academic Document Requests', 'desc' => 'Submit requests for official documents electronically.'],
                                ['icon' => 'fa-book-open', 'title' => 'Academic Record Services', 'desc' => 'Obtain certified official academic records.'],
                                ['icon' => 'fa-file-certificate', 'title' => 'Grades Certificate Services', 'desc' => 'Issue certified grades and estimation certificates.'],
                                ['icon' => 'fa-qrcode', 'title' => 'QR Document Verification', 'desc' => 'Electronically verify document authenticity via QR code.'],
                                ['icon' => 'fa-briefcase', 'title' => 'Job Opportunities', 'desc' => 'Post job opportunities and apply electronically.'],
                                ['icon' => 'fa-calendar-alt', 'title' => 'Events and Training', 'desc' => 'Register for various events and training programs.'],
                                ['icon' => 'fa-comments', 'title' => 'Graduate Communication', 'desc' => 'Direct communication channel between graduates and university administration.'],
                            ];
                    @endphp
                    @foreach($services as $service)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 h-100 rounded-3" style="background: #f8fafc; transition: all 0.3s ease;">
                                <div class="card-body text-center p-4">
                                    <div class="bg-primary bg-opacity-10 rounded-3 p-3 d-inline-block mb-3">
                                        <i class="fas {{ $service['icon'] }} fa-xl" style="color: var(--primary-blue);"></i>
                                    </div>
                                    <h6 class="fw-bold mb-2">{{ $service['title'] }}</h6>
                                    <p class="small text-muted mb-0">{{ $service['desc'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Contact CTA -->
        <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);">
            <div class="card-body p-4 p-md-5 text-center text-white">
                <h4 class="fw-bold mb-3">
                    {{ app()->getLocale() == 'ar' ? 'هل لديك استفسار؟' : 'Have a Question?' }}
                </h4>
                <p class="opacity-85 mb-4">
                    {{ app()->getLocale() == 'ar' ? 'لا تتردد في التواصل مع إدارة شؤون الخريجين لأي استفسار.' : 'Feel free to contact the Alumni Affairs Department for any inquiry.' }}
                </p>
                <a href="{{ route('alumni.contact') }}" class="btn btn-gold rounded-pill px-5 fw-bold">
                    <i class="fas fa-phone-alt me-2"></i>
                    {{ app()->getLocale() == 'ar' ? 'اتصل بنا' : 'Contact Us' }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
