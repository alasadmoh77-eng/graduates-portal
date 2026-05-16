@extends('layouts.app')

@section('title', __('app.my_profile'))

@section('content')
<div class="row mb-4">
    <div class="col-lg-10 mx-auto">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('graduate.dashboard') }}">{{ __('app.dashboard') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('app.my_profile') }}</li>
            </ol>
        </nav>

        <x-page-header 
            :title="__('app.my_profile')"
            icon="fa-user-circle"
        >
            <a href="{{ route('graduate.profile.edit') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-user-edit me-1"></i> {{ __('app.edit_profile') }}
            </a>
        </x-page-header>

        @if(!$user->graduate)
            <div class="alert alert-warning">{{ __('app.profile_no_graduate_record') }}</div>
        @else
            @php $g = $user->graduate; @endphp
            <div class="row g-4">
                <div class="col-md-4">
                    <x-section-card class="h-100 text-center">
                        @if($g->photo_url)
                            <img src="{{ $g->photo_url }}" alt="" class="rounded-circle mx-auto mb-3 shadow-sm border" style="width: 140px; height: 140px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 140px; height: 140px;">
                                <i class="fas fa-user fa-4x text-muted opacity-50"></i>
                            </div>
                        @endif
                        <h5 class="fw-bold mb-1 text-dark">{{ $user->name }}</h5>
                        <p class="text-muted small mb-0">{{ $user->email }}</p>
                    </x-section-card>
                </div>
                <div class="col-md-8">
                    <x-section-card 
                        :title="__('app.personal_information')"
                        icon="fa-address-card"
                    >
                        <dl class="row mb-0 small">
                            <dt class="col-sm-4 text-muted">{{ __('app.phone') }}</dt>
                            <dd class="col-sm-8 fw-bold">{{ $g->phone ?? '—' }}</dd>
                            <dt class="col-sm-4 text-muted mt-2">{{ __('app.university_id') }}</dt>
                            <dd class="col-sm-8 fw-bold mt-2">{{ $g->university_id }}</dd>
                            <dt class="col-sm-4 text-muted mt-2">{{ __('app.major') }}</dt>
                            <dd class="col-sm-8 fw-bold mt-2">{{ $g->major ? (app()->getLocale() === 'ar' ? $g->major->name_ar : $g->major->name_en) : '—' }}</dd>
                            <dt class="col-sm-4 text-muted mt-2">{{ __('app.graduation_year') }}</dt>
                            <dd class="col-sm-8 fw-bold mt-2">{{ $g->graduation_year }}</dd>
                        </dl>
                    </x-section-card>

                    <x-section-card 
                        :title="__('app.documents_files')"
                        icon="fa-file-alt"
                        class="mt-4"
                    >
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            @if($g->cvRelativePath() && \Illuminate\Support\Facades\Storage::disk('public')->exists($g->cvRelativePath()))
                                <a href="{{ route('graduate.profile.cv') }}" class="btn btn-outline-primary rounded-pill btn-sm fw-bold">
                                    <i class="fas fa-file-pdf me-1"></i> {{ __('app.download_cv') }}
                                </a>
                                <span class="badge bg-success bg-opacity-10 text-success py-2 px-3"><i class="fas fa-check-circle me-1"></i> {{ __('app.cv_on_file') }}</span>
                            @else
                                <span class="text-muted small"><i class="fas fa-exclamation-triangle me-1 text-warning"></i> {{ __('app.no_cv_on_file') }}</span>
                                <a href="{{ route('graduate.profile.edit') }}" class="btn btn-sm btn-warning rounded-pill fw-bold">{{ __('app.upload_cv') }}</a>
                            @endif
                        </div>
                    </x-section-card>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
