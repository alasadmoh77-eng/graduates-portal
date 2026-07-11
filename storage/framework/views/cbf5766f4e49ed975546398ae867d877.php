<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e(app()->getLocale() == 'ar' ? 'rtl' : 'ltr'); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title'); ?> | <?php echo e(__('app.app_name')); ?></title>

    <!-- Google Fonts - Tajawal for Arabic, Inter for English -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <?php if(app()->getLocale() == 'ar'): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.rtl.min.css')); ?>">
    <?php else: ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="<?php echo e(asset('assets/css/all.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/flatpickr.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/components.css')); ?>">
    <style>
        body {
            font-family:
                <?php echo e(app()->getLocale() == 'ar' ? "'Tajawal', sans-serif" : "'Inter', sans-serif"); ?>

            ;
        }
    </style>
    <?php echo $__env->yieldContent('styles'); ?>
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
                <?php echo e(app()->getLocale() == 'ar' ? 'جامعة إقليم سبأ — بوابة الخدمات الرقمية' : 'Saba Region University — Digital Services Portal'); ?>

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
                <img src="<?php echo e(asset('assets/images/university-logo.png')); ?>" alt="Saba Region University Logo" class="university-logo">
                <span class="d-none d-lg-inline logo-title"><?php echo e(__('app.app_name')); ?></span>
                <span class="d-inline d-lg-none logo-title-mobile"><?php echo e(app()->getLocale() == 'ar' ? 'بوابة الخريجين' : 'Graduates Portal'); ?></span>
            </a>

            <!-- Mobile Offcanvas Toggle Button -->
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNavbarOffcanvas" aria-controls="mobileNavbarOffcanvas">
                <i class="fas fa-bars fa-lg"></i>
            </button>

            <!-- Desktop Collapse Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->is('/') ? 'active' : ''); ?>"
                            href="/"><?php echo e(__('app.home')); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('verify.*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('verify.search')); ?>"><?php echo e(__('app.verify_doc')); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('alumni.about') ? 'active' : ''); ?>"
                            href="<?php echo e(route('alumni.about')); ?>"><?php echo e(__('app.about_alumni')); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('alumni.contact') ? 'active' : ''); ?>"
                            href="<?php echo e(route('alumni.contact')); ?>"><?php echo e(__('app.contact_us')); ?></a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo e(request()->routeIs('employer.*') ? 'active' : ''); ?>" href="#" id="employmentDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo e(app()->getLocale() == 'ar' ? 'الخدمات الوظيفية' : 'Employment Services'); ?>

                        </a>
                        <ul class="dropdown-menu shadow border-0" aria-labelledby="employmentDropdown">
                            <li><a class="dropdown-item" href="<?php echo e(route('employer.register')); ?>"><?php echo e(app()->getLocale() == 'ar' ? 'تسجيل جهة توظيف' : 'Employer Registration'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('employer.login')); ?>"><?php echo e(app()->getLocale() == 'ar' ? 'تسجيل الدخول' : 'Employer Login'); ?></a></li>
                            <?php if(auth()->guard()->check()): ?>
                                <?php if(Auth::user()->role === 'graduate'): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo e(route('graduate.jobs.index')); ?>"><?php echo e(app()->getLocale() == 'ar' ? 'فرص العمل المتاحة' : 'Job Opportunities'); ?></a></li>
                                    <li><a class="dropdown-item" href="<?php echo e(route('graduate.employers.index')); ?>"><?php echo e(__('app.partner_employers')); ?></a></li>
                                <?php endif; ?>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <?php if(auth()->guard()->guest()): ?>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-outline-light rounded-pill px-4"
                                href="<?php echo e(route('login')); ?>"><?php echo e(__('app.login')); ?></a>
                        </li>
                        <li class="nav-item ms-lg-1">
                            <a class="btn btn-gradient rounded-pill px-4 text-white fw-bold"
                                href="<?php echo e(route('register')); ?>"><?php echo e(__('app.register')); ?></a>
                        </li>
                    <?php else: ?>
                        <!-- Notifications Bell Dropdown -->
                        <li class="nav-item dropdown ms-lg-2">
                            <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-bell fa-lg"></i>
                                <?php $unreadCount = Auth::user()->unreadNotifications->count(); ?>
                                <?php if($unreadCount > 0): ?>
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger pulse-notify"
                                        style="font-size: 0.6rem;">
                                        <?php echo e($unreadCount); ?>

                                    </span>
                                <?php endif; ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0 notification-dropdown">
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold"><?php echo e(__('app.notifications')); ?></h6>
                                    <?php if($unreadCount > 0): ?>
                                        <a href="<?php echo e(route('notifications.markAllRead')); ?>"
                                            class="small text-decoration-none"><?php echo e(__('app.mark_all_read')); ?></a>
                                    <?php endif; ?>
                                </div>
                                <div class="overflow-auto" style="max-height: 300px;">
                                    <?php $__empty_1 = true; $__currentLoopData = Auth::user()->unreadNotifications->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <a href="<?php echo e($notification->data['link'] ?? '#'); ?>"
                                            class="dropdown-item p-3 border-bottom text-wrap">
                                            <?php if(in_array(($notification->data['type'] ?? ''), ['payment_proof_review', 'new_payment_proof_submitted'])): ?>
                                                <div class="small fw-bold text-warning">
                                                    <i class="fas fa-money-bill-wave me-1"></i><?php echo e($notification->data['title'] ?? 'طلب دفع جديد قيد المراجعة'); ?></div>
                                                <div class="small text-muted"><?php echo e($notification->data['message'] ?? ''); ?></div>
                                            <?php elseif(($notification->data['type'] ?? '') === 'new_graduate_registered'): ?>
                                                <div class="small fw-bold text-success">
                                                    <i class="fas fa-user-graduate me-1"></i><?php echo e($notification->data['title'] ?? 'تسجيل خريج جديد'); ?></div>
                                                <div class="small text-muted"><?php echo e($notification->data['message'] ?? ''); ?></div>
                                            <?php elseif(($notification->data['type'] ?? '') === 'signature_required'): ?>
                                                <div class="small fw-bold text-primary">
                                                    <i class="fas fa-pen-fancy me-1"></i><?php echo e($notification->data['title'] ?? 'توقيع مطلوب'); ?></div>
                                                <div class="small text-muted"><?php echo e($notification->data['message'] ?? ''); ?></div>
                                            <?php elseif(isset($notification->data['tracking_code'])): ?>
                                                <div class="small fw-bold text-primary">
                                                    <?php echo e($notification->data['tracking_code']); ?></div>
                                                <?php if(!empty($notification->data['old_status'])): ?>
                                                <div class="small text-muted">
                                                    <?php echo e(__('app.status_from')); ?> <?php echo e(__('app.document_status.'.$notification->data['old_status'])); ?></div>
                                                <?php endif; ?>
                                                <?php if(!empty($notification->data['new_status'])): ?>
                                                <div class="small text-muted">
                                                    <?php echo e(__('app.status_to')); ?> <?php echo e(__('app.document_status.'.$notification->data['new_status'])); ?></div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <div class="small fw-bold text-primary">
                                                    <?php echo e($notification->data['message'] ?? __('app.new_notification')); ?></div>
                                            <?php endif; ?>
                                            <div class="text-xs text-secondary mt-1" style="font-size: 0.7rem;">
                                                <?php echo e($notification->created_at->diffForHumans()); ?></div>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <div class="p-4 text-center text-muted"><?php echo e(__('app.no_new_notifications')); ?></div>
                                    <?php endif; ?>
                                </div>
                                <a href="<?php echo e(route('notifications.index')); ?>"
                                    class="dropdown-item p-2 text-center small text-primary border-top"><?php echo e(__('app.view_all_notifications')); ?></a>
                            </div>
                        </li>

                        <!-- User Profile Dropdown -->
                        <li class="nav-item dropdown ms-lg-2">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle fa-lg"></i> 
                                <span><?php echo e(Auth::user()->name); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li>
                                    <?php if(in_array(Auth::user()->role, ['admin','super_admin','academic_admin','finance_admin','employment_officer'])): ?>
                                        <?php if(Auth::user()->role === 'employment_officer'): ?>
                                            <a class="dropdown-item <?php echo e(request()->routeIs('admin.employment.dashboard') ? 'active' : ''); ?>"
                                                href="<?php echo e(route('admin.employment.dashboard')); ?>"><?php echo e(__('app.dashboard')); ?></a>
                                        <?php else: ?>
                                            <a class="dropdown-item <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>"
                                                href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('app.dashboard')); ?></a>
                                        <?php endif; ?>

                                        <?php if(in_array(Auth::user()->role, ['admin','super_admin','academic_admin'])): ?>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.graduate-registry.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('admin.graduate-registry.index')); ?>"><?php echo e(__('app.graduate_registry')); ?></a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.academic-records.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('admin.academic-records.import-form')); ?>">استيراد السجلات (Excel)</a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.requests.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('admin.requests.index')); ?>"><?php echo e(__('app.manage_requests')); ?></a>
                                        <?php endif; ?>

                                        <?php if(in_array(Auth::user()->role, ['admin','super_admin'])): ?>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.jobs.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('admin.jobs.index')); ?>"><?php echo e(__('app.manage_jobs')); ?></a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.events.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('admin.events.index')); ?>"><?php echo e(__('app.events_trainings')); ?></a>
                                        <?php endif; ?>

                                        <?php if(in_array(Auth::user()->role, ['admin','super_admin','employment_officer'])): ?>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.employers.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('admin.employers.index')); ?>"><?php echo e(app()->getLocale() == 'ar' ? 'إدارة جهات التوظيف' : 'Manage Employers'); ?></a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.employment.jobs.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('admin.employment.jobs.index')); ?>"><?php echo e(app()->getLocale() == 'ar' ? 'مراجعة الوظائف' : 'Job Moderation'); ?></a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.employment.applications.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('admin.employment.applications.index')); ?>"><?php echo e(app()->getLocale() == 'ar' ? 'طلبات التوظيف' : 'Manage Applications'); ?></a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.employment.analytics') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('admin.employment.analytics')); ?>"><?php echo e(app()->getLocale() == 'ar' ? 'تحليلات التوظيف' : 'Employment Analytics'); ?></a>
                                        <?php endif; ?>

                                        <?php if(in_array(Auth::user()->role, ['admin','super_admin','finance_admin'])): ?>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.payments.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('admin.payments.index')); ?>"><?php echo e(__('app.payment_review')); ?></a>
                                        <?php endif; ?>

                                        <?php if(in_array(Auth::user()->role, ['admin','super_admin'])): ?>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.faculties.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('admin.faculties.index')); ?>"><?php echo e(__('app.faculty_management')); ?></a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.admins.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('admin.admins.index')); ?>"><?php echo e(__('app.admin_management')); ?></a>
                                        <?php endif; ?>
                                    <?php elseif(Auth::user()->role == 'graduate'): ?>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('graduate.dashboard') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('graduate.dashboard')); ?>"><?php echo e(__('app.dashboard')); ?></a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('graduate.profile.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('graduate.profile.show')); ?>"><?php echo e(__('app.my_profile')); ?></a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('graduate.documents.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('graduate.documents.index')); ?>"><?php echo e(__('app.my_documents')); ?></a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('graduate.jobs.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('graduate.jobs.index')); ?>"><?php echo e(__('app.job_opportunities')); ?></a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('graduate.employers.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('graduate.employers.index')); ?>"><?php echo e(__('app.partner_employers')); ?></a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('graduate.applications.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('graduate.applications.index')); ?>"><?php echo e(app()->getLocale() == 'ar' ? 'طلبات التوظيف المقدمة' : 'My Job Applications'); ?></a>
                                    <?php else: ?>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('employer.dashboard') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('employer.dashboard')); ?>"><?php echo e(__('app.dashboard')); ?></a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('employer.jobs.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('employer.jobs.index')); ?>"><?php echo e(__('app.my_announced_jobs')); ?></a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('employer.applications.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('employer.applications.index')); ?>"><?php echo e(__('app.job_applications')); ?></a>
                                    <?php endif; ?>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="<?php echo e(route('logout')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                            class="dropdown-item text-danger d-flex align-items-center gap-2">
                                            <i class="fas fa-sign-out-alt"></i> <?php echo e(__('app.logout')); ?>

                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <!-- Language Switcher -->
                    <li class="nav-item ms-lg-2">
                        <?php if(app()->getLocale() == 'ar'): ?>
                            <a class="nav-link text-warning fw-bold" href="<?php echo e(route('lang.switch', 'en')); ?>">EN</a>
                        <?php else: ?>
                            <a class="nav-link text-warning fw-bold" href="<?php echo e(route('lang.switch', 'ar')); ?>">عربي</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Mobile Offcanvas Navigation Drawer -->
    <div class="offcanvas offcanvas-start offcanvas-navbar d-lg-none" tabindex="-1" id="mobileNavbarOffcanvas" aria-labelledby="mobileNavbarOffcanvasLabel">
        <div class="offcanvas-header d-flex justify-content-between align-items-center">
            <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2" id="mobileNavbarOffcanvasLabel">
                <img src="<?php echo e(asset('assets/images/university-logo.png')); ?>" alt="Logo" style="height: 38px; width: auto;">
                <span><?php echo e(app()->getLocale() == 'ar' ? 'بوابة الخريجين' : 'Graduates Portal'); ?></span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column justify-content-between">
            <ul class="navbar-nav gap-2">
                <!-- Global Links -->
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('/') ? 'active-mobile' : ''); ?>" href="/">
                        <i class="fas fa-home"></i> <?php echo e(__('app.home')); ?>

                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('verify.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('verify.search')); ?>">
                        <i class="fas fa-shield-alt"></i> <?php echo e(__('app.verify_doc')); ?>

                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('alumni.about') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('alumni.about')); ?>">
                        <i class="fas fa-info-circle"></i> <?php echo e(__('app.about_alumni')); ?>

                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('alumni.contact') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('alumni.contact')); ?>">
                        <i class="fas fa-envelope"></i> <?php echo e(__('app.contact_us')); ?>

                    </a>
                </li>
                <li class="nav-item">
                    <hr class="border-light opacity-10 my-2">
                    <span class="text-white-50 small px-3 d-block mb-1"><?php echo e(app()->getLocale() == 'ar' ? 'الخدمات الوظيفية' : 'Employment Services'); ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('employer.register') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('employer.register')); ?>">
                        <i class="fas fa-building"></i> <?php echo e(app()->getLocale() == 'ar' ? 'تسجيل جهة توظيف' : 'Employer Registration'); ?>

                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('employer.login') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('employer.login')); ?>">
                        <i class="fas fa-sign-in-alt"></i> <?php echo e(app()->getLocale() == 'ar' ? 'تسجيل الدخول' : 'Employer Login'); ?>

                    </a>
                </li>

                <?php if(auth()->guard()->check()): ?>
                    <hr class="border-light opacity-10 my-2">
                    
                    <?php if(in_array(Auth::user()->role, ['admin','super_admin','academic_admin','finance_admin','employment_officer'])): ?>
                        <li class="nav-item">
                            <?php if(Auth::user()->role === 'employment_officer'): ?>
                                <a class="nav-link <?php echo e(request()->routeIs('admin.employment.dashboard') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('admin.employment.dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt"></i> <?php echo e(__('app.dashboard')); ?>

                                </a>
                            <?php else: ?>
                                <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('admin.dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt"></i> <?php echo e(__('app.dashboard')); ?>

                                </a>
                            <?php endif; ?>
                        </li>

                        <?php if(in_array(Auth::user()->role, ['admin','super_admin','academic_admin'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.graduate-registry.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('admin.graduate-registry.index')); ?>">
                                <i class="fas fa-user-graduate"></i> <?php echo e(__('app.graduate_registry')); ?>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.academic-records.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('admin.academic-records.import-form')); ?>">
                                <i class="fas fa-file-excel"></i> استيراد السجلات (Excel)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.requests.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('admin.requests.index')); ?>">
                                <i class="fas fa-file-invoice"></i> <?php echo e(__('app.manage_requests')); ?>

                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if(in_array(Auth::user()->role, ['admin','super_admin'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.jobs.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('admin.jobs.index')); ?>">
                                <i class="fas fa-briefcase"></i> <?php echo e(__('app.manage_jobs')); ?>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.events.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('admin.events.index')); ?>">
                                <i class="fas fa-calendar-alt"></i> <?php echo e(__('app.events_trainings')); ?>

                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if(in_array(Auth::user()->role, ['admin','super_admin','employment_officer'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.employers.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('admin.employers.index')); ?>">
                                <i class="fas fa-building"></i> <?php echo e(app()->getLocale() == 'ar' ? 'إدارة جهات التوظيف' : 'Manage Employers'); ?>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.employment.jobs.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('admin.employment.jobs.index')); ?>">
                                <i class="fas fa-briefcase"></i> <?php echo e(app()->getLocale() == 'ar' ? 'مراجعة الوظائف' : 'Job Moderation'); ?>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.employment.applications.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('admin.employment.applications.index')); ?>">
                                <i class="fas fa-file-signature"></i> <?php echo e(app()->getLocale() == 'ar' ? 'طلبات التوظيف' : 'Manage Applications'); ?>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.employment.analytics') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('admin.employment.analytics')); ?>">
                                <i class="fas fa-chart-line"></i> <?php echo e(app()->getLocale() == 'ar' ? 'تحليلات التوظيف' : 'Employment Analytics'); ?>

                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if(in_array(Auth::user()->role, ['admin','super_admin','finance_admin'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.payments.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('admin.payments.index')); ?>">
                                <i class="fas fa-credit-card"></i> <?php echo e(__('app.payment_review')); ?>

                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if(in_array(Auth::user()->role, ['admin','super_admin'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.faculties.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('admin.faculties.index')); ?>">
                                <i class="fas fa-university"></i> <?php echo e(__('app.faculty_management')); ?>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.admins.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('admin.admins.index')); ?>">
                                <i class="fas fa-user-shield"></i> <?php echo e(__('app.admin_management')); ?>

                            </a>
                        </li>
                        <?php endif; ?>
                    <?php elseif(Auth::user()->role == 'graduate'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('graduate.dashboard') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('graduate.dashboard')); ?>">
                                <i class="fas fa-tachometer-alt"></i> <?php echo e(__('app.dashboard')); ?>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('graduate.profile.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('graduate.profile.show')); ?>">
                                <i class="fas fa-user"></i> <?php echo e(__('app.my_profile')); ?>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('graduate.documents.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('graduate.documents.index')); ?>">
                                <i class="fas fa-file-alt"></i> <?php echo e(__('app.my_documents')); ?>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('graduate.jobs.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('graduate.jobs.index')); ?>">
                                <i class="fas fa-briefcase"></i> <?php echo e(__('app.job_opportunities')); ?>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('graduate.employers.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('graduate.employers.index')); ?>">
                                <i class="fas fa-building"></i> <?php echo e(__('app.partner_employers')); ?>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('graduate.applications.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('graduate.applications.index')); ?>">
                                <i class="fas fa-file-alt"></i> <?php echo e(app()->getLocale() == 'ar' ? 'طلبات التوظيف المقدمة' : 'My Job Applications'); ?>

                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('employer.dashboard') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('employer.dashboard')); ?>">
                                <i class="fas fa-tachometer-alt"></i> <?php echo e(__('app.dashboard')); ?>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('employer.jobs.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('employer.jobs.index')); ?>">
                                <i class="fas fa-bullhorn"></i> <?php echo e(__('app.my_announced_jobs')); ?>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('employer.applications.*') ? 'active-mobile' : ''); ?>" href="<?php echo e(route('employer.applications.index')); ?>">
                                <i class="fas fa-user-tie"></i> <?php echo e(__('app.job_applications')); ?>

                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center justify-content-between" href="<?php echo e(route('notifications.index')); ?>">
                            <span><i class="fas fa-bell"></i> <?php echo e(__('app.notifications')); ?></span>
                            <?php if($unreadCount > 0): ?>
                                <span class="badge bg-danger rounded-pill"><?php echo e($unreadCount); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Mobile Auth Action Drawer Footer -->
            <div class="mt-4 pt-3 border-top border-light border-opacity-10">
                <?php if(auth()->guard()->guest()): ?>
                    <div class="d-grid gap-2">
                        <a class="btn btn-outline-light w-100 rounded-pill" href="<?php echo e(route('login')); ?>"><?php echo e(__('app.login')); ?></a>
                        <a class="btn btn-gradient w-100 rounded-pill" href="<?php echo e(route('register')); ?>"><?php echo e(__('app.register')); ?></a>
                    </div>
                <?php else: ?>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="fas fa-user-circle fa-2x text-warning"></i>
                        <span class="fw-bold"><?php echo e(Auth::user()->name); ?></span>
                    </div>
                    <form action="<?php echo e(route('logout')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-danger w-100 rounded-pill d-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-sign-out-alt"></i> <?php echo e(__('app.logout')); ?>

                        </button>
                    </form>
                <?php endif; ?>

                <!-- Mobile Language Switch -->
                <div class="text-center mt-3">
                    <?php if(app()->getLocale() == 'ar'): ?>
                        <a class="text-warning fw-bold text-decoration-none" href="<?php echo e(route('lang.switch', 'en')); ?>">Switch to English</a>
                    <?php else: ?>
                        <a class="text-warning fw-bold text-decoration-none" href="<?php echo e(route('lang.switch', 'ar')); ?>">التحويل إلى العربية</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <main class="py-5">
        <div class="container">
            <?php if(session('success')): ?>
                <div class="alert alert-success ds-alert ds-alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle ds-alert-icon"></i>
                    <div>
                        <?php echo e(session('success')); ?>

                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger ds-alert ds-alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle ds-alert-icon"></i>
                    <div>
                        <?php echo e(session('error')); ?>

                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <!-- Professional University Footer -->
    <footer>
        <div class="container">
            <div class="row g-4">
                <!-- Branding and Description -->
                <div class="col-lg-4 col-md-6">
                    <h5 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                        <img src="<?php echo e(asset('assets/images/university-logo.png')); ?>" alt="Logo" style="height: 38px; width: auto;">
                        <span><?php echo e(__('app.app_name')); ?></span>
                    </h5>
                    <p class="small text-muted mb-4">
                        <?php echo e(app()->getLocale() == 'ar' 
                            ? 'بوابة الخدمات الرقمية لجامعة إقليم سبأ، لتيسير شؤون الخريجين، والربط الفعال مع الجهات الموظفة، وتأمين صحة الوثائق إلكترونياً.' 
                            : 'Digital services portal of Saba Region University to streamline graduate requests, enable employer connection, and digitally verify records.'); ?>

                    </p>
                    <div class="footer-social d-flex gap-3">
                        <a href="https://www.facebook.com/wsa.usr?locale=ar_AR" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.youtube.com/channel/UCZF1gnR_VW1GI1epjqym5xg" target="_blank" title="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="https://wa.me/967780641221" target="_blank" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                <!-- Service Links Column -->
                <div class="col-lg-4 col-md-6">
                    <h5><?php echo e(app()->getLocale() == 'ar' ? 'خدمات الخريجين' : 'Alumni Services'); ?></h5>
                    <div class="row">
                        <div class="col-6">
                            <ul class="list-unstyled d-flex flex-column gap-2 small">
                                <li><a href="/"><i class="fas fa-angle-left me-1"></i> <?php echo e(__('app.home')); ?></a></li>
                                <li><a href="<?php echo e(route('verify.search')); ?>"><i class="fas fa-angle-left me-1"></i> <?php echo e(__('app.verify_doc')); ?></a></li>
                                <li><a href="<?php echo e(route('alumni.about')); ?>"><i class="fas fa-angle-left me-1"></i> <?php echo e(__('app.about_alumni')); ?></a></li>
                                <li><a href="<?php echo e(route('alumni.contact')); ?>"><i class="fas fa-angle-left me-1"></i> <?php echo e(__('app.contact_us')); ?></a></li>
                                <li><a href="<?php echo e(route('login')); ?>"><i class="fas fa-angle-left me-1"></i> <?php echo e(__('app.login')); ?></a></li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul class="list-unstyled d-flex flex-column gap-2 small">
                                <li><a href="<?php echo e(route('graduate.documents.create')); ?>"><i class="fas fa-angle-left me-1"></i> <?php echo e(app()->getLocale() == 'ar' ? 'الخدمات الأكاديمية' : 'Academic Services'); ?></a></li>
                                <li><a href="<?php echo e(route('graduate.jobs.index')); ?>"><i class="fas fa-angle-left me-1"></i> <?php echo e(app()->getLocale() == 'ar' ? 'فرص العمل' : 'Job Opportunities'); ?></a></li>
                                <li><a href="<?php echo e(route('graduate.employers.index')); ?>"><i class="fas fa-angle-left me-1"></i> <?php echo e(__('app.partner_employers')); ?></a></li>
                                <li><a href="<?php echo e(route('events.public')); ?>"><i class="fas fa-angle-left me-1"></i> <?php echo e(app()->getLocale() == 'ar' ? 'الفعاليات والتدريب' : 'Events & Training'); ?></a></li>
                                <li><a href="<?php echo e(route('employer.register')); ?>"><i class="fas fa-angle-left me-1"></i> <?php echo e(app()->getLocale() == 'ar' ? 'تسجيل جهة توظيف' : 'Employer Registration'); ?></a></li>
                                <li><a href="<?php echo e(route('employer.login')); ?>"><i class="fas fa-angle-left me-1"></i> <?php echo e(app()->getLocale() == 'ar' ? 'دخول جهات التوظيف' : 'Employer Login'); ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Column — University Official -->
                <div class="col-lg-4 col-md-12">
                    <h5><?php echo e(app()->getLocale() == 'ar' ? 'معلومات الاتصال' : 'Contact Information'); ?></h5>
                    <ul class="list-unstyled footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo e(app()->getLocale() == 'ar' ? 'الجمهورية اليمنية – مأرب' : 'Republic of Yemen – Marib'); ?></span>
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
                <p class="mb-0">&copy; <?php echo e(date('Y')); ?> <?php echo e(__('app.app_name')); ?> — <?php echo e(app()->getLocale() == 'ar' ? 'جامعة إقليم سبأ' : 'Saba Region University'); ?>. <?php echo e(app()->getLocale() == 'ar' ? 'جميع الحقوق محفوظة.' : 'All rights reserved.'); ?></p>
            </div>
        </div>
    </footer>

    <!-- Toast Notifications Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <?php if(Auth::check() && Auth::user()->unreadNotifications->count() > 0): ?>
            <?php $latest = Auth::user()->unreadNotifications->first(); ?>
            <div id="liveToast" class="toast show shadow-lg border-0 rounded-4" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="toast-header bg-primary text-white border-0 rounded-top-4 p-3">
                    <i class="fas fa-bell me-2"></i>
                    <strong class="me-auto"><?php echo e(__('app.new_notification')); ?></strong>
                    <small><?php echo e($latest->created_at->diffForHumans()); ?></small>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body p-3">
                    <p class="mb-2"><?php echo e(__('app.update_in_request')); ?>

                        <strong><?php echo e($latest->data['tracking_code'] ?? ''); ?></strong></p>
                    <a href="<?php echo e($latest->data['link'] ?? '#'); ?>"
                        class="btn btn-sm btn-primary rounded-pill px-3"><?php echo e(__('app.open_link')); ?></a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/jquery-3.7.0.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/flatpickr.js')); ?>"></script>

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
    <?php echo $__env->yieldContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\Users\RTX\Desktop\myproject\ملفات المشروع\المعدلهgraduates-portal3.22\resources\views/layouts/app.blade.php ENDPATH**/ ?>