@extends('layouts.app')

@section('title', __('app.register'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card p-4 shadow-lg">
            <div class="text-center mb-4">
                <h2 class="fw-bold">{{ __('app.register') }}</h2>
                <p class="text-muted">{{ __('app.register_subtitle') }}</p>
            </div>
            
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.name') }}</label>
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
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.university_id') }}</label>
                        <input type="text" name="university_id" class="form-control @error('university_id') is-invalid @enderror" value="{{ old('university_id') }}" required placeholder="e.g. 2020-001">
                        @error('university_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.phone') }}</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.major') }}</label>
                        <select name="major_id" class="form-select @error('major_id') is-invalid @enderror" required>
                            <option value="">{{ __('app.select_major') }}</option>
                            @foreach($majors as $major)
                                <option value="{{ $major->id }}" {{ old('major_id') == $major->id ? 'selected' : '' }}>
                                    {{ app()->getLocale() == 'ar' ? $major->name_ar : $major->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('major_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.graduation_year') }}</label>
                        <input type="number" name="graduation_year" class="form-control @error('graduation_year') is-invalid @enderror" value="{{ old('graduation_year') }}" required min="2000" max="{{ date('Y') }}">
                        @error('graduation_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
            </div>
        </div>
    </div>
</div>
@endsection
