@extends('layouts.app')

@section('title', __('app.add_new_faculty'))

@section('styles')
<style>
    .form-card { background: white; border-radius: 1rem; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
    .form-label { font-weight: 700; font-size: 0.85rem; color: #134074; }
    .form-control, .form-select {
        border-radius: 0.6rem;
        border: 1.5px solid #e5e7eb;
        padding: 0.65rem 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #134074;
        box-shadow: 0 0 0 3px rgba(19,64,116,0.08);
    }
    .section-divider {
        border-top: 2px solid #f1f5f9;
        margin: 1.8rem 0;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none fw-bold"><i class="fas fa-home me-1"></i>{{ __('app.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.faculties.index') }}" class="text-decoration-none fw-bold">{{ __('app.faculty_management') }}</a></li>
                <li class="breadcrumb-item active">{{ __('app.add_new_faculty') }}</li>
            </ol>
        </nav>

        <div class="form-card p-4 p-md-5">
            {{-- Header --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-4" style="width:52px;height:52px;">
                    <i class="fas fa-university fa-lg"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark">{{ __('app.add_new_faculty') }}</h4>
                    <p class="text-muted small mb-0">{{ __('app.add_new_faculty_hint') }}</p>
                </div>
            </div>

            <div class="section-divider"></div>

            <form action="{{ route('admin.faculties.store') }}" method="POST" novalidate>
                @csrf

                {{-- Arabic Name --}}
                <div class="mb-4">
                    <label for="name_ar" class="form-label">
                        <i class="fas fa-university me-1 text-primary"></i> {{ __('app.faculty_name_ar') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name_ar" id="name_ar"
                        class="form-control @error('name_ar') is-invalid @enderror"
                        value="{{ old('name_ar') }}"
                        placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل اسم الكلية باللغة العربية' : 'Enter faculty name in Arabic' }}"
                        required>
                    @error('name_ar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- English Name --}}
                <div class="mb-4">
                    <label for="name_en" class="form-label">
                        <i class="fas fa-university me-1 text-primary"></i> {{ __('app.faculty_name_en') }}
                    </label>
                    <input type="text" name="name_en" id="name_en"
                        class="form-control @error('name_en') is-invalid @enderror"
                        value="{{ old('name_en') }}"
                        placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل اسم الكلية باللغة الإنجليزية (اختياري)' : 'Enter faculty name in English (optional)' }}">
                    @error('name_en')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-4">
                    <label for="description" class="form-label">
                        <i class="fas fa-align-right me-1 text-primary"></i> {{ __('app.description') }}
                    </label>
                    <textarea name="description" id="description" rows="4"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="{{ app()->getLocale() == 'ar' ? 'اكتب وصفاً للكلية (اختياري)' : 'Write a description for the faculty (optional)' }}">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="mb-4">
                    <label for="status" class="form-label">
                        <i class="fas fa-toggle-on me-1 text-primary"></i> {{ __('app.status') }} <span class="text-danger">*</span>
                    </label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>{{ __('app.active') }}</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>{{ __('app.inactive') }}</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="section-divider"></div>

                {{-- Submit Buttons --}}
                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('admin.faculties.index') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold">
                        {{ __('app.cancel') }}
                    </a>
                    <button type="submit" class="btn btn-gradient rounded-pill px-5 py-2 fw-bold text-white shadow">
                        {{ __('app.submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
