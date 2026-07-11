@extends('layouts.app')

@section('title', 'Employer Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold">{{ __('app.employer_dashboard') }}</h2>
        <p class="text-muted">{{ __('app.welcome_back') }}, {{ Auth::user()->employer->company_name ?? Auth::user()->name }}</p>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-briefcase text-primary fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="card-title fw-bold mb-0">{{ __('app.manage_job_posts') }}</h5>
                        <small class="text-muted">{{ __('app.manage_job_posts_desc') }}</small>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <a href="{{ route('employer.jobs.create') }}" class="btn btn-primary">{{ __('app.post_new_job') }}</a>
                    <a href="{{ route('employer.jobs.index') }}" class="btn btn-outline-primary">{{ __('app.view_my_listings') }}</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-user-tie text-success fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="card-title fw-bold mb-0">{{ __('app.applications_title') }}</h5>
                        <small class="text-muted">{{ __('app.applications_desc') }}</small>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <a href="{{ route('employer.applications.index') }}" class="btn btn-outline-success">{{ __('app.view_all_applications') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
