<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ __('app.app_name') }}</title>

    <!-- Google Fonts - Tajawal for Arabic, Inter for English -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.rtl.min.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    @endif

    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/flatpickr.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <style>
        body {
            font-family:
                {{ app()->getLocale() == 'ar' ? "'Tajawal', sans-serif" : "'Inter', sans-serif" }}
            ;
        }
    </style>
    @yield('styles')
</head>

<body>
    <!-- University Top Bar -->
    <div class="university-topbar">
        <div class="container d-flex justify-content-between align-items-center">
            <span>
                <i class="fas fa-phone-alt me-1"></i> +967 6 302008
                <span class="mx-2 d-none d-sm-inline">|</span>
                <span class="d-none d-sm-inline"><i class="fas fa-envelope me-1"></i> INFO@USR.AC</span>
            </span>
            <span class="d-none d-md-inline">
                {{ app()->getLocale() == 'ar' ? 'جامعة إقليم سبأ — بوابة الخدمات الرقمية' : 'Saba Region University — Digital Services Portal' }}
            </span>
            <span>
                <a href="https://www.usr.ac" target="_blank"><i class="fas fa-external-link-alt me-1"></i> usr.ac</a>
            </span>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <!-- Branding / Logo -->
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="/">
                <img src="{{ asset('assets/images/university-logo.png') }}" alt="Saba Region University Logo" class="university-logo">
                <span class="d-none d-lg-inline logo-title">{{ __('app.app_name') }}</span>
                <span class="d-inline d-lg-none logo-title-mobile">{{ app()->getLocale() == 'ar' ? 'بوابة الخريجين' : 'Graduates Portal' }}</span>
            </a>

            <!-- Mobile Offcanvas Toggle Button -->
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNavbarOffcanvas" aria-controls="mobileNavbarOffcanvas">
                <i class="fas fa-bars fa-lg"></i>
            </button>

            <!-- Desktop Collapse Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}"
                            href="/">{{ __('app.home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('verify.*') ? 'active' : '' }}"
                            href="{{ route('verify.search') }}">{{ __('app.verify_doc') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('alumni.about') ? 'active' : '' }}"
                            href="{{ route('alumni.about') }}">{{ __('app.about_alumni') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('alumni.contact') ? 'active' : '' }}"
                            href="{{ route('alumni.contact') }}">{{ __('app.contact_us') }}</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('employer.*') ? 'active' : '' }}" href="#" id="employmentDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ app()->getLocale() == 'ar' ? 'الخدمات الوظيفية' : 'Employment Services' }}
                        </a>
                        <ul class="dropdown-menu shadow border-0" aria-labelledby="employmentDropdown">
                            <li><a class="dropdown-item" href="{{ route('employer.register') }}">{{ app()->getLocale() == 'ar' ? 'تسجيل جهة توظيف' : 'Employer Registration' }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('employer.login') }}">{{ app()->getLocale() == 'ar' ? 'تسجيل الدخول' : 'Employer Login' }}</a></li>
                            @auth
                                @if(Auth::user()->role === 'graduate')
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('graduate.jobs.index') }}">{{ app()->getLocale() == 'ar' ? 'فرص العمل المتاحة' : 'Job Opportunities' }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('graduate.employers.index') }}">{{ __('app.partner_employers') }}</a></li>
                                @endif
                            @endauth
                        </ul>
                    </li>

                    @guest
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-outline-light rounded-pill px-4"
                                href="{{ route('login') }}">{{ __('app.login') }}</a>
                        </li>
                        <li class="nav-item ms-lg-1">
                            <a class="btn btn-gradient rounded-pill px-4 text-white fw-bold"
                                href="{{ route('register') }}">{{ __('app.register') }}</a>
                        </li>
                    @else
                        <!-- Notifications Bell Dropdown -->
                        <li class="nav-item dropdown ms-lg-2">
                            <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-bell fa-lg"></i>
                                @php $unreadCount = Auth::user()->unreadNotifications->count(); @endphp
                                @if($unreadCount > 0)
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger pulse-notify"
                                        style="font-size: 0.6rem;">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0 notification-dropdown">
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold">{{ __('app.notifications') }}</h6>
                                    @if($unreadCount > 0)
                                        <a href="{{ route('notifications.markAllRead') }}"
                                            class="small text-decoration-none">{{ __('app.mark_all_read') }}</a>
                                    @endif
                                </div>
                                <div class="overflow-auto" style="max-height: 300px;">
                                    @forelse(Auth::user()->unreadNotifications->take(5) as $notification)
                                        <a href="{{ $notification->data['link'] ?? '#' }}"
                                            class="dropdown-item p-3 border-bottom text-wrap">
                                            @if(in_array(($notification->data['type'] ?? ''), ['payment_proof_review', 'new_payment_proof_submitted']))
                                                <div class="small fw-bold text-warning">
                                                    <i class="fas fa-money-bill-wave me-1"></i>{{ $notification->data['title'] ?? 'طلب دفع جديد قيد المراجعة' }}</div>
                                                <div class="small text-muted">{{ $notification->data['message'] ?? '' }}</div>
                                            @elseif(($notification->data['type'] ?? '') === 'new_graduate_registered')
                                                <div class="small fw-bold text-success">
                                                    <i class="fas fa-user-graduate me-1"></i>{{ $notification->data['title'] ?? 'تسجيل خريج جديد' }}</div>
                                                <div class="small text-muted">{{ $notification->data['message'] ?? '' }}</div>
                                            @elseif(($notification->data['type'] ?? '') === 'signature_required')
                                                <div class="small fw-bold text-primary">
                                                    <i class="fas fa-pen-fancy me-1"></i>{{ $notification->data['title'] ?? 'توقيع مطلوب' }}</div>
                                                <div class="small text-muted">{{ $notification->data['message'] ?? '' }}</div>
                                            @elseif(isset($notification->data['tracking_code']))
                                                <div class="small fw-bold text-primary">
                                                    {{ $notification->data['tracking_code'] }}</div>
                                                @if(!empty($notification->data['old_status']))
                                                <div class="small text-muted">
                                                    {{ __('app.status_from') }} {{ __('app.document_status.'.$notification->data['old_status']) }}</div>
                                                @endif
                                                @if(!empty($notification->data['new_status']))
                                                <div class="small text-muted">
                                                    {{ __('app.status_to') }} {{ __('app.document_status.'.$notification->data['new_status']) }}</div>
                                                @endif
                                            @else
                                                <div class="small fw-bold text-primary">
                                                    {{ $notification->data['message'] ?? __('app.new_notification') }}</div>
                                            @endif
                                            <div class="text-xs text-secondary mt-1" style="font-size: 0.7rem;">
                                                {{ $notification->created_at->diffForHumans() }}</div>
                                        </a>
                                    @empty
                                        <div class="p-4 text-center text-muted">{{ __('app.no_new_notifications') }}</div>
                                    @endforelse
                                </div>
                                <a href="{{ route('notifications.index') }}"
                                    class="dropdown-item p-2 text-center small text-primary border-top">{{ __('app.view_all_notifications') }}</a>
                            </div>
                        </li>

                        <!-- User Profile Dropdown -->
                        <li class="nav-item dropdown ms-lg-2">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle fa-lg"></i> 
                                <span>{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li>
                                    @if(in_array(Auth::user()->role, ['admin','super_admin','academic_admin','finance_admin','employment_officer']))
                                        @if(Auth::user()->role === 'employment_officer')
                                            <a class="dropdown-item {{ request()->routeIs('admin.employment.dashboard') ? 'active' : '' }}"
                                                href="{{ route('admin.employment.dashboard') }}">{{ __('app.dashboard') }}</a>
                                        @else
                                            <a class="dropdown-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                                href="{{ route('admin.dashboard') }}">{{ __('app.dashboard') }}</a>
                                        @endif

                                        @if(in_array(Auth::user()->role, ['admin','super_admin','academic_admin']))
                                        <a class="dropdown-item {{ request()->routeIs('admin.graduate-registry.*') ? 'active' : '' }}"
                                            href="{{ route('admin.graduate-registry.index') }}">{{ __('app.graduate_registry') }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('admin.academic-records.*') ? 'active' : '' }}"
                                            href="{{ route('admin.academic-records.import-form') }}">استيراد السجلات (Excel)</a>
                                        <a class="dropdown-item {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}"
                                            href="{{ route('admin.requests.index') }}">{{ __('app.manage_requests') }}</a>
                                        @endif

                                        @if(in_array(Auth::user()->role, ['admin','super_admin','finance_admin']))
                                        <a class="dropdown-item {{ request()->routeIs('admin.document-fees.*') ? 'active' : '' }}"
                                            href="{{ route('admin.document-fees.index') }}">إدارة رسوم الوثائق</a>
                                        @endif

                                        @if(in_array(Auth::user()->role, ['admin','super_admin']))
                                        <a class="dropdown-item {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}"
                                            href="{{ route('admin.jobs.index') }}">{{ __('app.manage_jobs') }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('admin.events.*') ? 'active' : '' }}"
                                            href="{{ route('admin.events.index') }}">{{ __('app.events_trainings') }}</a>
                                        @endif

                                        @if(in_array(Auth::user()->role, ['admin','super_admin','employment_officer']))
                                        <a class="dropdown-item {{ request()->routeIs('admin.employers.*') ? 'active' : '' }}"
                                            href="{{ route('admin.employers.index') }}">{{ app()->getLocale() == 'ar' ? 'إدارة جهات التوظيف' : 'Manage Employers' }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('admin.employment.jobs.*') ? 'active' : '' }}"
                                            href="{{ route('admin.employment.jobs.index') }}">{{ app()->getLocale() == 'ar' ? 'مراجعة الوظائف' : 'Job Moderation' }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('admin.employment.applications.*') ? 'active' : '' }}"
                                            href="{{ route('admin.employment.applications.index') }}">{{ app()->getLocale() == 'ar' ? 'طلبات التوظيف' : 'Manage Applications' }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('admin.employment.analytics') ? 'active' : '' }}"
                                            href="{{ route('admin.employment.analytics') }}">{{ app()->getLocale() == 'ar' ? 'تحليلات التوظيف' : 'Employment Analytics' }}</a>
                                        @endif

                                        @if(in_array(Auth::user()->role, ['admin','super_admin','finance_admin']))
                                        <a class="dropdown-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}"
                                            href="{{ route('admin.payments.index') }}">{{ __('app.payment_review') }}</a>
                                        @endif

                                        @if(in_array(Auth::user()->role, ['admin','super_admin']))
                                        <a class="dropdown-item {{ request()->routeIs('admin.faculties.*') ? 'active' : '' }}"
                                            href="{{ route('admin.faculties.index') }}">{{ __('app.faculty_management') }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}"
                                            href="{{ route('admin.admins.index') }}">{{ __('app.admin_management') }}</a>
                                        @endif
                                    @elseif(Auth::user()->role == 'graduate')
                                        <a class="dropdown-item {{ request()->routeIs('graduate.dashboard') ? 'active' : '' }}"
                                            href="{{ route('graduate.dashboard') }}">{{ __('app.dashboard') }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('graduate.profile.*') ? 'active' : '' }}"
                                            href="{{ route('graduate.profile.show') }}">{{ __('app.my_profile') }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('graduate.documents.*') ? 'active' : '' }}"
                                            href="{{ route('graduate.documents.index') }}">{{ __('app.my_documents') }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('graduate.jobs.*') ? 'active' : '' }}"
                                            href="{{ route('graduate.jobs.index') }}">{{ __('app.job_opportunities') }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('graduate.employers.*') ? 'active' : '' }}"
                                            href="{{ route('graduate.employers.index') }}">{{ __('app.partner_employers') }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('graduate.applications.*') ? 'active' : '' }}"
                                            href="{{ route('graduate.applications.index') }}">{{ app()->getLocale() == 'ar' ? 'طلبات التوظيف المقدمة' : 'My Job Applications' }}</a>
                                    @else
                                        <a class="dropdown-item {{ request()->routeIs('employer.dashboard') ? 'active' : '' }}"
                                            href="{{ route('employer.dashboard') }}">{{ __('app.dashboard') }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('employer.jobs.*') ? 'active' : '' }}"
                                            href="{{ route('employer.jobs.index') }}">{{ __('app.my_announced_jobs') }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('employer.applications.*') ? 'active' : '' }}"
                                            href="{{ route('employer.applications.index') }}">{{ __('app.job_applications') }}</a>
                                    @endif
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="dropdown-item text-danger d-flex align-items-center gap-2">
                                            <i class="fas fa-sign-out-alt"></i> {{ __('app.logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest

                    <!-- Language Switcher -->
                    <li class="nav-item ms-lg-2">
                        @if(app()->getLocale() == 'ar')
                            <a class="nav-link text-warning fw-bold" href="{{ route('lang.switch', 'en') }}">EN</a>
                        @else
                            <a class="nav-link text-warning fw-bold" href="{{ route('lang.switch', 'ar') }}">عربي</a>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Mobile Offcanvas Navigation Drawer -->
    <div class="offcanvas offcanvas-start offcanvas-navbar d-lg-none" tabindex="-1" id="mobileNavbarOffcanvas" aria-labelledby="mobileNavbarOffcanvasLabel">
        <div class="offcanvas-header d-flex justify-content-between align-items-center">
            <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2" id="mobileNavbarOffcanvasLabel">
                <img src="{{ asset('assets/images/university-logo.png') }}" alt="Logo" style="height: 38px; width: auto;">
                <span>{{ app()->getLocale() == 'ar' ? 'بوابة الخريجين' : 'Graduates Portal' }}</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column justify-content-between">
            <ul class="navbar-nav gap-2">
                <!-- Global Links -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active-mobile' : '' }}" href="/">
                        <i class="fas fa-home"></i> {{ __('app.home') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('verify.*') ? 'active-mobile' : '' }}" href="{{ route('verify.search') }}">
                        <i class="fas fa-shield-alt"></i> {{ __('app.verify_doc') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('alumni.about') ? 'active-mobile' : '' }}" href="{{ route('alumni.about') }}">
                        <i class="fas fa-info-circle"></i> {{ __('app.about_alumni') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('alumni.contact') ? 'active-mobile' : '' }}" href="{{ route('alumni.contact') }}">
                        <i class="fas fa-envelope"></i> {{ __('app.contact_us') }}
                    </a>
                </li>
                <li class="nav-item">
                    <hr class="border-light opacity-10 my-2">
                    <span class="text-white-50 small px-3 d-block mb-1">{{ app()->getLocale() == 'ar' ? 'الخدمات الوظيفية' : 'Employment Services' }}</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('employer.register') ? 'active-mobile' : '' }}" href="{{ route('employer.register') }}">
                        <i class="fas fa-building"></i> {{ app()->getLocale() == 'ar' ? 'تسجيل جهة توظيف' : 'Employer Registration' }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('employer.login') ? 'active-mobile' : '' }}" href="{{ route('employer.login') }}">
                        <i class="fas fa-sign-in-alt"></i> {{ app()->getLocale() == 'ar' ? 'تسجيل الدخول' : 'Employer Login' }}
                    </a>
                </li>

                @auth
                    <hr class="border-light opacity-10 my-2">
                    
                    @if(in_array(Auth::user()->role, ['admin','super_admin','academic_admin','finance_admin','employment_officer']))
                        <li class="nav-item">
                            @if(Auth::user()->role === 'employment_officer')
                                <a class="nav-link {{ request()->routeIs('admin.employment.dashboard') ? 'active-mobile' : '' }}" href="{{ route('admin.employment.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> {{ __('app.dashboard') }}
                                </a>
                            @else
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active-mobile' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> {{ __('app.dashboard') }}
                                </a>
                            @endif
                        </li>

                        @if(in_array(Auth::user()->role, ['admin','super_admin','academic_admin']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.graduate-registry.*') ? 'active-mobile' : '' }}" href="{{ route('admin.graduate-registry.index') }}">
                                <i class="fas fa-user-graduate"></i> {{ __('app.graduate_registry') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.academic-records.*') ? 'active-mobile' : '' }}" href="{{ route('admin.academic-records.import-form') }}">
                                <i class="fas fa-file-excel"></i> استيراد السجلات (Excel)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.requests.*') ? 'active-mobile' : '' }}" href="{{ route('admin.requests.index') }}">
                                <i class="fas fa-file-invoice"></i> {{ __('app.manage_requests') }}
                            </a>
                        </li>
                        @endif

                        @if(in_array(Auth::user()->role, ['admin','super_admin','finance_admin']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.document-fees.*') ? 'active-mobile' : '' }}" href="{{ route('admin.document-fees.index') }}">
                                <i class="fas fa-money-bill-wave"></i> إدارة رسوم الوثائق
                            </a>
                        </li>
                        @endif

                        @if(in_array(Auth::user()->role, ['admin','super_admin']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.jobs.*') ? 'active-mobile' : '' }}" href="{{ route('admin.jobs.index') }}">
                                <i class="fas fa-briefcase"></i> {{ __('app.manage_jobs') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.events.*') ? 'active-mobile' : '' }}" href="{{ route('admin.events.index') }}">
                                <i class="fas fa-calendar-alt"></i> {{ __('app.events_trainings') }}
                            </a>
                        </li>
                        @endif

                        @if(in_array(Auth::user()->role, ['admin','super_admin','employment_officer']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.employers.*') ? 'active-mobile' : '' }}" href="{{ route('admin.employers.index') }}">
                                <i class="fas fa-building"></i> {{ app()->getLocale() == 'ar' ? 'إدارة جهات التوظيف' : 'Manage Employers' }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.employment.jobs.*') ? 'active-mobile' : '' }}" href="{{ route('admin.employment.jobs.index') }}">
                                <i class="fas fa-briefcase"></i> {{ app()->getLocale() == 'ar' ? 'مراجعة الوظائف' : 'Job Moderation' }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.employment.applications.*') ? 'active-mobile' : '' }}" href="{{ route('admin.employment.applications.index') }}">
                                <i class="fas fa-file-signature"></i> {{ app()->getLocale() == 'ar' ? 'طلبات التوظيف' : 'Manage Applications' }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.employment.analytics') ? 'active-mobile' : '' }}" href="{{ route('admin.employment.analytics') }}">
                                <i class="fas fa-chart-line"></i> {{ app()->getLocale() == 'ar' ? 'تحليلات التوظيف' : 'Employment Analytics' }}
                            </a>
                        </li>
                        @endif

                        @if(in_array(Auth::user()->role, ['admin','super_admin','finance_admin']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active-mobile' : '' }}" href="{{ route('admin.payments.index') }}">
                                <i class="fas fa-credit-card"></i> {{ __('app.payment_review') }}
                            </a>
                        </li>
                        @endif

                        @if(in_array(Auth::user()->role, ['admin','super_admin']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.faculties.*') ? 'active-mobile' : '' }}" href="{{ route('admin.faculties.index') }}">
                                <i class="fas fa-university"></i> {{ __('app.faculty_management') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active-mobile' : '' }}" href="{{ route('admin.admins.index') }}">
                                <i class="fas fa-user-shield"></i> {{ __('app.admin_management') }}
                            </a>
                        </li>
                        @endif
                    @elseif(Auth::user()->role == 'graduate')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('graduate.dashboard') ? 'active-mobile' : '' }}" href="{{ route('graduate.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> {{ __('app.dashboard') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('graduate.profile.*') ? 'active-mobile' : '' }}" href="{{ route('graduate.profile.show') }}">
                                <i class="fas fa-user"></i> {{ __('app.my_profile') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('graduate.documents.*') ? 'active-mobile' : '' }}" href="{{ route('graduate.documents.index') }}">
                                <i class="fas fa-file-alt"></i> {{ __('app.my_documents') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('graduate.jobs.*') ? 'active-mobile' : '' }}" href="{{ route('graduate.jobs.index') }}">
                                <i class="fas fa-briefcase"></i> {{ __('app.job_opportunities') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('graduate.employers.*') ? 'active-mobile' : '' }}" href="{{ route('graduate.employers.index') }}">
                                <i class="fas fa-building"></i> {{ __('app.partner_employers') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('graduate.applications.*') ? 'active-mobile' : '' }}" href="{{ route('graduate.applications.index') }}">
                                <i class="fas fa-file-alt"></i> {{ app()->getLocale() == 'ar' ? 'طلبات التوظيف المقدمة' : 'My Job Applications' }}
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employer.dashboard') ? 'active-mobile' : '' }}" href="{{ route('employer.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> {{ __('app.dashboard') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employer.jobs.*') ? 'active-mobile' : '' }}" href="{{ route('employer.jobs.index') }}">
                                <i class="fas fa-bullhorn"></i> {{ __('app.my_announced_jobs') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employer.applications.*') ? 'active-mobile' : '' }}" href="{{ route('employer.applications.index') }}">
                                <i class="fas fa-user-tie"></i> {{ __('app.job_applications') }}
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center justify-content-between" href="{{ route('notifications.index') }}">
                            <span><i class="fas fa-bell"></i> {{ __('app.notifications') }}</span>
                            @if($unreadCount > 0)
                                <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </li>
                @endauth
            </ul>

            <!-- Mobile Auth Action Drawer Footer -->
            <div class="mt-4 pt-3 border-top border-light border-opacity-10">
                @guest
                    <div class="d-grid gap-2">
                        <a class="btn btn-outline-light w-100 rounded-pill" href="{{ route('login') }}">{{ __('app.login') }}</a>
                        <a class="btn btn-gradient w-100 rounded-pill" href="{{ route('register') }}">{{ __('app.register') }}</a>
                    </div>
                @else
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="fas fa-user-circle fa-2x text-warning"></i>
                        <span class="fw-bold">{{ Auth::user()->name }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 rounded-pill d-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-sign-out-alt"></i> {{ __('app.logout') }}
                        </button>
                    </form>
                @endguest

                <!-- Mobile Language Switch -->
                <div class="text-center mt-3">
                    @if(app()->getLocale() == 'ar')
                        <a class="text-warning fw-bold text-decoration-none" href="{{ route('lang.switch', 'en') }}">Switch to English</a>
                    @else
                        <a class="text-warning fw-bold text-decoration-none" href="{{ route('lang.switch', 'ar') }}">التحويل إلى العربية</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <main class="py-5">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success ds-alert ds-alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle ds-alert-icon"></i>
                    <div>
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger ds-alert ds-alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle ds-alert-icon"></i>
                    <div>
                        {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Professional University Footer -->
    <footer>
        <div class="container">
            <div class="row g-4">
                <!-- Branding and Description -->
                <div class="col-lg-4 col-md-6">
                    <h5 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                        <img src="{{ asset('assets/images/university-logo.png') }}" alt="Logo" style="height: 38px; width: auto;">
                        <span>{{ __('app.app_name') }}</span>
                    </h5>
                    <p class="small text-muted mb-4">
                        {{ app()->getLocale() == 'ar' 
                            ? 'بوابة الخدمات الرقمية لجامعة إقليم سبأ، لتيسير شؤون الخريجين، والربط الفعال مع الجهات الموظفة، وتأمين صحة الوثائق إلكترونياً.' 
                            : 'Digital services portal of Saba Region University to streamline graduate requests, enable employer connection, and digitally verify records.' }}
                    </p>
                    <div class="footer-social d-flex gap-3">
                        <a href="https://www.facebook.com/wsa.usr?locale=ar_AR" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.youtube.com/channel/UCZF1gnR_VW1GI1epjqym5xg" target="_blank" title="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="https://wa.me/967780641221" target="_blank" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                <!-- Service Links Column -->
                <div class="col-lg-4 col-md-6">
                    <h5>{{ app()->getLocale() == 'ar' ? 'خدمات الخريجين' : 'Alumni Services' }}</h5>
                    <div class="row">
                        <div class="col-6">
                            <ul class="list-unstyled d-flex flex-column gap-2 small">
                                <li><a href="/"><i class="fas fa-angle-left me-1"></i> {{ __('app.home') }}</a></li>
                                <li><a href="{{ route('verify.search') }}"><i class="fas fa-angle-left me-1"></i> {{ __('app.verify_doc') }}</a></li>
                                <li><a href="{{ route('alumni.about') }}"><i class="fas fa-angle-left me-1"></i> {{ __('app.about_alumni') }}</a></li>
                                <li><a href="{{ route('alumni.contact') }}"><i class="fas fa-angle-left me-1"></i> {{ __('app.contact_us') }}</a></li>
                                <li><a href="{{ route('login') }}"><i class="fas fa-angle-left me-1"></i> {{ __('app.login') }}</a></li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul class="list-unstyled d-flex flex-column gap-2 small">
                                <li><a href="{{ route('graduate.documents.create') }}"><i class="fas fa-angle-left me-1"></i> {{ app()->getLocale() == 'ar' ? 'الخدمات الأكاديمية' : 'Academic Services' }}</a></li>
                                <li><a href="{{ route('graduate.jobs.index') }}"><i class="fas fa-angle-left me-1"></i> {{ app()->getLocale() == 'ar' ? 'فرص العمل' : 'Job Opportunities' }}</a></li>
                                <li><a href="{{ route('graduate.employers.index') }}"><i class="fas fa-angle-left me-1"></i> {{ __('app.partner_employers') }}</a></li>
                                <li><a href="{{ route('events.public') }}"><i class="fas fa-angle-left me-1"></i> {{ app()->getLocale() == 'ar' ? 'الفعاليات والتدريب' : 'Events & Training' }}</a></li>
                                <li><a href="{{ route('employer.register') }}"><i class="fas fa-angle-left me-1"></i> {{ app()->getLocale() == 'ar' ? 'تسجيل جهة توظيف' : 'Employer Registration' }}</a></li>
                                <li><a href="{{ route('employer.login') }}"><i class="fas fa-angle-left me-1"></i> {{ app()->getLocale() == 'ar' ? 'دخول جهات التوظيف' : 'Employer Login' }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Column — University Official -->
                <div class="col-lg-4 col-md-12">
                    <h5>{{ app()->getLocale() == 'ar' ? 'معلومات الاتصال' : 'Contact Information' }}</h5>
                    <ul class="list-unstyled footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ app()->getLocale() == 'ar' ? 'الجمهورية اليمنية – مأرب' : 'Republic of Yemen – Marib' }}</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span><a href="tel:+9676302008">+9676302008</a></span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span><a href="tel:+9676301274">+9676301274</a></span>
                        </li>
                        <li>
                            <i class="fab fa-whatsapp"></i>
                            <span><a href="https://wa.me/967780641221" target="_blank">780641221</a></span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span><a href="mailto:INFO@USR.AC">INFO@USR.AC</a></span>
                        </li>
                        <li>
                            <i class="fas fa-globe"></i>
                            <span><a href="https://www.usr.ac" target="_blank">www.usr.ac</a></span>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="border-light opacity-10 my-4">
            
            <div class="text-center small text-muted">
                <p class="mb-0">&copy; {{ date('Y') }} {{ __('app.app_name') }} — {{ app()->getLocale() == 'ar' ? 'جامعة إقليم سبأ' : 'Saba Region University' }}. {{ app()->getLocale() == 'ar' ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}</p>
            </div>
        </div>
    </footer>

    <!-- Toast Notifications Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
            @php $latest = Auth::user()->unreadNotifications->first(); @endphp
            <div id="liveToast" class="toast show shadow-lg border-0 rounded-4" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="toast-header bg-primary text-white border-0 rounded-top-4 p-3">
                    <i class="fas fa-bell me-2"></i>
                    <strong class="me-auto">{{ __('app.new_notification') }}</strong>
                    <small>{{ $latest->created_at->diffForHumans() }}</small>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body p-3">
                    <p class="mb-2">{{ __('app.update_in_request') }}
                        <strong>{{ $latest->data['tracking_code'] ?? '' }}</strong></p>
                    <a href="{{ $latest->data['link'] ?? '#' }}"
                        class="btn btn-sm btn-primary rounded-pill px-3">{{ __('app.open_link') }}</a>
                </div>
            </div>
        @endif
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/flatpickr.js') }}"></script>

    <script>
        $(document).ready(function () {
            setTimeout(function () {
                $('.toast').toast('hide');
            }, 5000);

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (el) {
                new bootstrap.Tooltip(el);
            });

            // Initialize Flatpickr for Date Inputs
            if (typeof flatpickr !== 'undefined') {
                flatpickr('.date-picker-input', {
                    dateFormat: 'Y-m-d',
                    allowInput: false,
                    clickOpens: true,
                    disableMobile: true,
                    locale: 'en'
                });
            }
        });
    </script>
    @yield('scripts')
</body>

</html>
