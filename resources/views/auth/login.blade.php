@extends('layouts.app')

@section('title', __('app.login'))

@section('content')
<div class="row justify-content-center align-items-center min-vh-50">
    <div class="col-md-5">
        <div class="card p-4 shadow-lg">
            <div class="text-center mb-4">
                <h2 class="fw-bold">{{ __('app.login') }}</h2>
                <p class="text-muted">{{ __('app.welcome_back') }}</p>
            </div>
            
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('app.email') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">{{ __('app.password') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0 @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3 form-check d-flex justify-content-between">
                    <div>
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">{{ __('app.remember_me') }}</label>
                    </div>
                    <span class="text-muted small btn-disabled-state"
                          data-bs-toggle="tooltip"
                          data-bs-placement="top"
                          title="{{ __('app.coming_soon') }}">
                        {{ __('app.forgot_password') }}
                    </span>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                    {{ __('app.login') }}
                </button>
            </form>
            
            <div class="text-center mt-4 pt-2 border-top">
                <p class="mb-0 text-muted">
                    {{ __('app.dont_have_account') }} 
                    <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">{{ __('app.register') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
