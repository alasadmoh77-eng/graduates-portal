@extends('layouts.app')

@section('title', __('app.new_event'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary mb-1">{{ __('app.new_event') }}</h2>
                <p class="text-muted mb-0 small">إضافة فعالية أو تدريب يظهر للخريجين عندما تكون الحالة «قادمة».</p>
            </div>
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary rounded-pill px-4">{{ __('app.cancel') }}</a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form action="{{ route('admin.events.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">العنوان (عربي) <span class="text-danger">*</span></label>
                            <input type="text" name="title_ar" class="form-control @error('title_ar') is-invalid @enderror"
                                   value="{{ old('title_ar') }}" required maxlength="255">
                            @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Title (English) <span class="text-danger">*</span></label>
                            <input type="text" name="title_en" class="form-control @error('title_en') is-invalid @enderror"
                                   value="{{ old('title_en') }}" required maxlength="255" dir="ltr">
                            @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">الوصف (عربي) <span class="text-danger">*</span></label>
                            <textarea name="description_ar" class="form-control @error('description_ar') is-invalid @enderror" rows="4" required>{{ old('description_ar') }}</textarea>
                            @error('description_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Description (English) <span class="text-danger">*</span></label>
                            <textarea name="description_en" class="form-control @error('description_en') is-invalid @enderror" rows="4" required dir="ltr">{{ old('description_en') }}</textarea>
                            @error('description_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('app.date') }} والوقت <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_at" class="form-control @error('start_at') is-invalid @enderror"
                                   value="{{ old('start_at') }}" required>
                            @error('start_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('app.location') }}</label>
                            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                                   value="{{ old('location') }}" maxlength="255">
                            @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">الحد الأقصى للمقاعد</label>
                            <input type="number" name="seats" class="form-control @error('seats') is-invalid @enderror"
                                   value="{{ old('seats') }}" min="1" placeholder="—">
                            <small class="text-muted">اتركه فارغاً لعدد غير محدود</small>
                            @error('seats')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('admin.events.index') }}" class="btn btn-light px-4">{{ __('app.cancel') }}</a>
                        <button type="submit" class="btn btn-primary px-5 rounded-pill">{{ __('app.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
