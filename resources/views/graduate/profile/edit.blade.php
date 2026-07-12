@extends('layouts.app')

@section('title', __('app.edit_profile'))

@section('content')
<div class="row mb-4">
    <div class="col-lg-10 mx-auto">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('graduate.profile.show') }}">{{ __('app.my_profile') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('app.edit_profile') }}</li>
            </ol>
        </nav>

        <h2 class="fw-bold text-primary mb-4">{{ __('app.edit_profile') }}</h2>

        @if(!$user->graduate)
            <div class="alert alert-warning">{{ __('app.profile_no_graduate_record') }}</div>
        @else
            @php $g = $user->graduate; @endphp
            <form action="{{ route('graduate.profile.update') }}" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm rounded-4">
                @csrf
                @method('PUT')
                <div class="card-body p-4">
                    <h5 class="fw-bold border-bottom pb-2 mb-3">{{ __('app.personal_information') }}</h5>
                    
                    <div class="alert alert-info py-2 px-3 mb-3 small d-flex align-items-center rounded-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <span>لا يمكن تعديل الاسم أو البريد الإلكتروني أو التخصص أو سنة التخرج من حساب الخريج؛ لأنها بيانات أكاديمية رسمية. يرجى التواصل مع الإدارة عند الحاجة إلى تصحيحها.</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('app.name') }}</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('app.email') }}</label>
                            <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly dir="ltr">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('app.phone') }}</label>
                            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $g->phone) }}" maxlength="30" inputmode="numeric" pattern="[0-9]*" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('app.university_id') }}</label>
                            <input type="text" class="form-control" value="{{ $g->university_id }}" disabled readonly>
                            <small class="text-muted">{{ __('app.university_id_readonly') }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('app.major') }}</label>
                            <input type="text" class="form-control bg-light" value="{{ app()->getLocale() === 'ar' ? $g->major?->name_ar : $g->major?->name_en }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('app.graduation_year') }}</label>
                            <input type="text" class="form-control bg-light" value="{{ $g->graduation_year }}" readonly>
                        </div>
                    </div>

                    <h5 class="fw-bold border-bottom pb-2 mb-3 mt-4">{{ __('app.profile_photo') }}</h5>
                    @if($g->photo_url)
                        <div class="mb-2">
                            <img src="{{ $g->photo_url }}" alt="" class="rounded-3" style="max-height: 120px;">
                        </div>
                    @endif
                    <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                    <small class="text-muted">{{ __('app.profile_photo_hint') }}</small>
                    @error('photo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

                    <h5 class="fw-bold border-bottom pb-2 mb-3 mt-4">{{ __('app.cv_resume') }}</h5>
                    @if($g->cvRelativePath() && \Illuminate\Support\Facades\Storage::disk('public')->exists($g->cvRelativePath()))
                        <p class="small text-success mb-2"><i class="fas fa-check-circle me-1"></i> {{ __('app.cv_on_file') }} — <a href="{{ route('graduate.profile.cv') }}">{{ __('app.download_cv') }}</a></p>
                    @endif
                    <input type="file" name="cv" class="form-control @error('cv') is-invalid @enderror" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                    <small class="text-muted">{{ __('app.profile_cv_hint') }}</small>
                    @error('cv')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <a href="{{ route('graduate.profile.show') }}" class="btn btn-light px-4">{{ __('app.cancel') }}</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">{{ __('app.save_changes') }}</button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
