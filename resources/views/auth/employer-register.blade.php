@extends('layouts.app')

@section('title', __('app.register') . ' - ' . __('app.employer', [], 'en'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card p-4 shadow-lg">
            <div class="text-center mb-4">
                <h2 class="fw-bold">{{ __('app.employer_register_title') }}</h2>
                <p class="text-muted">{{ __('app.employer_register_subtitle') }}</p>
            </div>
            
            <form action="{{ route('employer.register') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.rep_name') }}</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.email') }}</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">{{ __('app.company_name') }}</label>
                        <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" required>
                        @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.password') }}</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.confirm_password') }}</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                    {{ __('app.register') }}
                </button>
            </form>
            
            <div class="text-center mt-4 pt-2 border-top">
                <p class="mb-0 text-muted">
                    {{ __('app.already_have_account') }} 
                    <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">{{ __('app.login') }}</a>
                </p>
                <p class="mb-0 mt-3">
                    <a href="{{ route('register') }}" class="text-secondary text-decoration-none">{{ __('app.are_you_graduate') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
