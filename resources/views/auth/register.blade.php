@extends('layouts.app')

@section('title', __('app.register'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card p-4 shadow-lg position-relative">
            <a href="/" class="btn-close position-absolute top-0 end-0 m-3" aria-label="Close" title="{{ app()->getLocale() == 'ar' ? 'الرجوع للرئيسية' : 'Back to Home' }}"></a>
            <div class="text-center mb-4">
                <h2 class="fw-bold">{{ __('app.register') }}</h2>
                <p class="text-muted">{{ __('app.register_subtitle') }}</p>
            </div>
            
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.name') }}</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required readonly placeholder="{{ app()->getLocale() == 'ar' ? 'سيتم التعبئة تلقائياً عند إدخال الرقم الجامعي' : 'Will be filled automatically' }}">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.email') }}</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.university_id') }}</label>
                        <input type="text" name="university_id" id="university_id" class="form-control @error('university_id') is-invalid @enderror" value="{{ old('university_id') }}" required placeholder="e.g. 2020-001" inputmode="numeric" pattern="[0-9]*" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        @error('university_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <div id="university-feedback" class="invalid-feedback d-block" style="display: none;"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">{{ __('app.phone') }}</label>
                        <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" inputmode="numeric" pattern="[0-9]*" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">{{ __('app.faculty') }}</label>
                        <input type="text" id="faculty_name" class="form-control" readonly placeholder="{{ app()->getLocale() == 'ar' ? 'سيتم التعبئة تلقائياً عند إدخال الرقم الجامعي' : 'Will be filled automatically' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">{{ __('app.major') }}</label>
                        <input type="text" id="major_name" class="form-control" readonly placeholder="{{ app()->getLocale() == 'ar' ? 'سيتم التعبئة تلقائياً عند إدخال الرقم الجامعي' : 'Will be filled automatically' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">{{ __('app.graduation_year') }}</label>
                        <input type="text" id="graduation_year" class="form-control" readonly placeholder="{{ app()->getLocale() == 'ar' ? 'سيتم التعبئة تلقائياً عند إدخال الرقم الجامعي' : 'Will be filled automatically' }}">
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
                    لديك حساب بالفعل؟
                    <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">تسجيل الدخول</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var universityIdInput = $('#university_id');
        var nameInput = $('#name');
        var emailInput = $('#email');
        var facultyInput = $('#faculty_name');
        var majorInput = $('#major_name');
        var gradYearInput = $('#graduation_year');
        var feedbackDiv = $('#university-feedback');

        function checkGraduate() {
            var val = universityIdInput.val().trim();
            if (val.length === 0) {
                nameInput.val('');
                emailInput.val('').removeAttr('readonly');
                facultyInput.val('');
                majorInput.val('');
                gradYearInput.val('');
                universityIdInput.removeClass('is-valid is-invalid');
                feedbackDiv.text('').hide();
                return;
            }

            // Show checking indicator
            feedbackDiv.removeClass('invalid-feedback text-success').addClass('text-info').text('جاري التحقق من سجلات الجامعة...').show();

            $.ajax({
                url: '/api/check-graduate/' + encodeURIComponent(val),
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        universityIdInput.removeClass('is-invalid').addClass('is-valid');
                        feedbackDiv.removeClass('invalid-feedback text-info').addClass('text-success').text('تم التحقق من الرقم الجامعي بنجاح: ' + response.graduate.name).show();
                        
                        nameInput.val(response.graduate.name);
                        facultyInput.val(response.graduate.college || '');
                        majorInput.val(response.graduate.major);
                        gradYearInput.val(response.graduate.graduation_year);

                        if (response.graduate.email && response.graduate.email.trim() !== '') {
                            emailInput.val(response.graduate.email).attr('readonly', true);
                        } else {
                            emailInput.val('').removeAttr('readonly');
                        }
                    } else {
                        universityIdInput.removeClass('is-valid').addClass('is-invalid');
                        feedbackDiv.removeClass('text-success text-info').addClass('invalid-feedback').text(response.message).show();
                        
                        nameInput.val('');
                        emailInput.val('').removeAttr('readonly');
                        facultyInput.val('');
                        majorInput.val('');
                        gradYearInput.val('');
                    }
                },
                error: function() {
                    feedbackDiv.removeClass('text-success text-info').addClass('invalid-feedback').text('حدث خطأ أثناء الاتصال بالخادم. يرجى المحاولة لاحقاً.').show();
                }
            });
        }

        // Trigger check on blur and change
        universityIdInput.on('blur change', checkGraduate);

        // Also trigger check if there is an initial value
        if (universityIdInput.val().trim().length > 0) {
            checkGraduate();
        }
    });
</script>
@endsection
