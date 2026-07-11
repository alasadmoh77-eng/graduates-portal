@extends('layouts.app')

@section('title', __('app.add_new_admin'))

@section('styles')
<style>
    .form-card { background: white; border-radius: 1rem; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
    .form-label { font-weight: 700; font-size: 0.85rem; color: #7b2a2eff; }
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
                <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}" class="text-decoration-none fw-bold">{{ __('app.admin_management') }}</a></li>
                <li class="breadcrumb-item active">{{ __('app.add_new_admin') }}</li>
            </ol>
        </nav>

        <div class="form-card p-4 p-md-5">
            {{-- Header --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-4" style="width:52px;height:52px;">
                    <i class="fas fa-user-plus fa-lg"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark">{{ __('app.add_new_admin') }}</h4>
                    <p class="text-muted small mb-0">{{ __('app.add_new_admin_hint') }}</p>
                </div>
            </div>

            <div class="section-divider"></div>

            <form action="{{ route('admin.admins.store') }}" method="POST" id="create-admin-form" novalidate>
                @csrf

                {{-- Name --}}
                <div class="mb-4">
                    <label for="name" class="form-label">
                        <i class="fas fa-user me-1 text-primary"></i> {{ __('app.full_name') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        placeholder="{{ __('app.full_name_placeholder') }}"
                        autocomplete="name" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope me-1 text-primary"></i> {{ __('app.email') }} <span class="text-danger">*</span>
                    </label>
                    <input type="email" name="email" id="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="admin@university.edu.ye"
                        autocomplete="email" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-1 text-primary"></i> {{ __('app.password') }} <span class="text-danger">*</span>
                    </label>
                    <input type="password" name="password" id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="••••••••" autocomplete="new-password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted small mt-1"><i class="fas fa-info-circle me-1"></i>{{ __('app.password_min_hint') }}</div>
                </div>

                {{-- Confirm Password --}}
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-lock me-1 text-primary"></i> {{ __('app.confirm_password') }} <span class="text-danger">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="form-control"
                        placeholder="••••••••" autocomplete="new-password" required>
                </div>

                <div class="section-divider"></div>

                <div class="row g-4">
                    {{-- Signer Role --}}
                    <div class="col-md-6">
                        <label for="signer_role" class="form-label">
                            <i class="fas fa-file-signature me-1 text-primary"></i> المنصب التوقيعي
                        </label>
                        <select name="signer_role" id="signer_role" class="form-select">
                            <option value="">-- بدون منصب توقيعي --</option>
                            <option value="عميد الكلية" {{ old('signer_role') == 'عميد الكلية' ? 'selected' : '' }}>عميد الكلية</option>
                            <option value="مسجل الكلية" {{ old('signer_role') == 'مسجل الكلية' ? 'selected' : '' }}>مسجل الكلية</option>
                            <option value="مدير إدارة شؤون الخريجين" {{ old('signer_role') == 'مدير إدارة شؤون الخريجين' ? 'selected' : '' }}>مدير إدارة شؤون الخريجين</option>
                            <option value="المختص الأكاديمي" {{ old('signer_role') == 'المختص الأكاديمي' ? 'selected' : '' }}>المختص الأكاديمي</option>
                            <option value="المسجل العام" {{ old('signer_role') == 'المسجل العام' ? 'selected' : '' }}>المسجل العام</option>
                            <option value="نائب رئيس الجامعة لشؤون الطلاب" {{ old('signer_role') == 'نائب رئيس الجامعة لشؤون الطلاب' ? 'selected' : '' }}>نائب رئيس الجامعة لشؤون الطلاب</option>
                        </select>
                    </div>

                    {{-- Role --}}
                    <div class="col-md-6">
                        <label for="role" class="form-label">
                            <i class="fas fa-id-badge me-1 text-primary"></i> {{ __('app.role') }} <span class="text-danger">*</span>
                        </label>
                        <select name="role" id="role"
                            class="form-select @error('role') is-invalid @enderror" required>
                            <option value="" disabled selected>{{ __('app.select_role') }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                    {{ __('app.roles.' . $role) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6">
                        <label for="is_active" class="form-label">
                            <i class="fas fa-toggle-on me-1 text-primary"></i> {{ __('app.account_status') }} <span class="text-danger">*</span>
                        </label>
                        <select name="is_active" id="is_active"
                            class="form-select @error('is_active') is-invalid @enderror" required>
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>{{ __('app.active') }}</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>{{ __('app.inactive') }}</option>
                        </select>
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="section-divider"></div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">
                        <i class="fas fa-arrow-right me-1"></i> {{ __('app.cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" id="submit-btn">
                        <i class="fas fa-user-plus me-2"></i> {{ __('app.create_admin') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
