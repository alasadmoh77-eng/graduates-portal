@extends('layouts.app')

@section('title', __('app.access_denied'))

@section('content')
<div class="row justify-content-center min-vh-75 py-5">
    <div class="col-lg-6 text-center py-5">
        <div class="mb-4">
            <i class="fas fa-shield-halved fa-5x text-danger opacity-50"></i>
        </div>
        <h1 class="display-4 fw-bold text-dark mb-3">403</h1>
        <h4 class="fw-bold text-dark mb-3">{{ __('app.access_denied') }}</h4>
        <p class="text-muted mb-4 fs-5">{{ __('app.access_denied_message') }}</p>
        <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-5 py-3 fw-bold">
            <i class="fas fa-arrow-right me-2"></i>{{ __('app.go_back') }}
        </a>
    </div>
</div>
@endsection



