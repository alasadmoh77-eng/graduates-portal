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
            
            <form action="{{ route('employer.register') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Section 1: Contact Person Information -->
                <h5 class="fw-bold mb-3 text-primary border-bottom pb-2">
                    <i class="fas fa-user-tie me-1"></i> {{ app()->getLocale() == 'ar' ? 'بيانات ممثل الجهة (شخص التواصل)' : 'Contact Person Details' }}
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.rep_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني للتواصل' : 'Contact Email' }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <!-- Section 2: Company Information -->
                <h5 class="fw-bold mb-3 mt-4 text-primary border-bottom pb-2">
                    <i class="fas fa-building me-1"></i> {{ app()->getLocale() == 'ar' ? 'بيانات الشركة / المؤسسة' : 'Company Details' }}
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.company_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" required>
                        @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ app()->getLocale() == 'ar' ? 'مجال العمل / القطاع' : 'Industry / Sector' }} <span class="text-danger">*</span></label>
                        <input type="text" name="industry" class="form-control @error('industry') is-invalid @enderror" value="{{ old('industry') }}" placeholder="{{ app()->getLocale() == 'ar' ? 'مثال: تقنية معلومات، تعليم، صحة' : 'e.g. IT, Education, Health' }}" required>
                        @error('industry') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone Number' }} <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ app()->getLocale() == 'ar' ? 'الموقع الإلكتروني (اختياري)' : 'Website (Optional)' }}</label>
                        <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website') }}" placeholder="https://example.com">
                        @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">{{ app()->getLocale() == 'ar' ? 'عنوان الشركة / المقر الرئيسي' : 'Company Address' }} <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" required>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">{{ app()->getLocale() == 'ar' ? 'وصف الشركة' : 'Company Description' }} <span class="text-danger">*</span></label>
                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">{{ app()->getLocale() == 'ar' ? 'شعار الشركة (اختياري)' : 'Company Logo (Optional)' }}</label>
                        <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                        <div class="form-text text-muted small">{{ app()->getLocale() == 'ar' ? 'صيغ الصور المسموحة: JPG, PNG, WEBP. الحجم الأقصى: 2 ميجابايت' : 'Allowed types: JPG, PNG, WEBP. Max size: 2MB' }}</div>
                        @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <!-- Section 3: Credentials -->
                <h5 class="fw-bold mb-3 mt-4 text-primary border-bottom pb-2">
                    <i class="fas fa-lock me-1"></i> {{ app()->getLocale() == 'ar' ? 'بيانات الحساب وكلمة المرور' : 'Account Password' }}
                </h5>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.password') }} <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.confirm_password') }} <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                    {{ app()->getLocale() == 'ar' ? 'تسجيل جديد' : 'Register' }}
                </button>
            </form>
            
            <div class="text-center mt-4 pt-2 border-top">
                <p class="mb-0 text-muted">
                    لديك حساب بالفعل؟
                    <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">تسجيل الدخول</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
