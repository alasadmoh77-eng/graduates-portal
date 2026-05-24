<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ __('app.app_name') }}</title>

    <!-- Bootstrap CSS -->
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.rtl.min.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    @endif

    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <style>
        body {
            font-family:
                {{ app()->getLocale() == 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }}
            ;
        }
    </style>
    @yield('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-university me-2"></i> {{ __('app.app_name') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'fw-bold text-white border-bottom border-2 border-white' : '' }}"
                            href="/">{{ __('app.home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('verify.*') ? 'fw-bold text-white border-bottom border-2 border-white' : '' }}"
                            href="{{ route('verify.show') }}">{{ __('app.verify_doc') }}</a>
                    </li>

                    @guest
                        <li class="nav-item ms-lg-3">
                            <a class="btn btn-outline-light rounded-pill px-4"
                                href="{{ route('login') }}">{{ __('app.login') }}</a>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-light rounded-pill px-4 text-primary fw-bold"
                                href="{{ route('register') }}">{{ __('app.register') }}</a>
                        </li>
                    @else
                        <!-- Notifications Bell -->
                        <li class="nav-item dropdown ms-lg-3">
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
                                            @if(isset($notification->data['tracking_code']))
                                                <div class="small fw-bold text-primary">{{ __('app.status_update') }}
                                                    {{ $notification->data['tracking_code'] }}</div>
                                                <div class="small text-muted">{{ __('app.status_to') }}
                                                    {{ $notification->data['new_status'] ?? '' }}</div>
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

                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle fa-lg me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li>
                                    @if(Auth::user()->role == 'admin')
                                        <a class="dropdown-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                            href="{{ route('admin.dashboard') }}">{{ __('app.dashboard') }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}"
                                            href="{{ route('admin.requests.index') }}">{{ __('app.manage_requests') }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}"
                                            href="{{ route('admin.jobs.index') }}">{{ __('app.manage_jobs') }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('admin.events.*') ? 'active' : '' }}"
                                            href="{{ route('admin.events.index') }}">{{ __('app.events_trainings') }}</a>
                                    @elseif(Auth::user()->role == 'graduate')
                                        <a class="dropdown-item {{ request()->routeIs('graduate.dashboard') ? 'active' : '' }}"
                                            href="{{ route('graduate.dashboard') }}">{{ __('app.dashboard') }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('graduate.profile.*') ? 'active' : '' }}"
                                            href="{{ route('graduate.profile.show') }}">{{ __('app.my_profile') }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('graduate.documents.*') ? 'active' : '' }}"
                                            href="{{ route('graduate.documents.index') }}">{{ __('app.my_documents') }}</a>
                                        <a class="dropdown-item {{ request()->routeIs('graduate.jobs.*') ? 'active' : '' }}"
                                            href="{{ route('graduate.jobs.index') }}">{{ __('app.job_opportunities') }}</a>
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
                                            class="dropdown-item text-danger">{{ __('app.logout') }}</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest

                    <li class="nav-item ms-lg-3">
                        @if(app()->getLocale() == 'ar')
                            <a class="nav-link" href="{{ route('lang.switch', 'en') }}">English</a>
                        @else
                            <a class="nav-link" href="{{ route('lang.switch', 'ar') }}">العربية</a>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-5">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer>
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} {{ __('app.app_name') }}. All rights reserved.</p>
        </div>
    </footer>

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

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.7.0.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            setTimeout(function () {
                $('.toast').toast('hide');
            }, 5000);

            // Initialize Bootstrap tooltips globally
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (el) {
                new bootstrap.Tooltip(el);
            });
        });
    </script>
    @yield('scripts')
</body>

</html>