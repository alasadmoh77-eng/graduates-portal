@extends('layouts.app')

@section('title', __('app.request_document'))

@section('styles')
    <style>
        :root {
            --request-primary: var(--primary-color);
            --request-dark: var(--primary-dark);
            --request-hover: var(--primary-hover);
            --request-soft: var(--primary-soft);
            --request-gold: var(--accent-gold);
        }

        .request-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .form-section {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(16, 93, 130, 0.06);
            border: 1px solid rgba(16, 93, 130, 0.06);
        }

        .section-title {
            font-weight: 800;
            color: var(--request-primary);
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .doc-type-card {
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            height: 100%;
            background-color: white;
        }

        .btn-check:checked+.doc-type-card {
            border-color: var(--request-primary);
            background-color: var(--request-soft);
            box-shadow: 0 10px 20px rgba(16, 93, 130, 0.12);
        }

        .btn-check:checked+.doc-type-card::after {
            content: '\f058';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 15px;
            left: 15px;
            color: var(--request-primary);
            font-size: 1.25rem;
        }

        .doc-type-card:hover {
            border-color: var(--request-dark);
            background-color: rgba(16, 93, 130, 0.04);
            transform: translateY(-2px);
        }

        .icon-circle {
            width: 50px;
            height: 50px;
            background: var(--request-soft);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            color: var(--request-primary);
        }

        .custom-input {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.9rem 1.1rem;
            font-size: 0.95rem;
            transition: all 0.2s;
            background: #fcfcfc;
        }

        .custom-input:focus {
            border-color: var(--request-primary);
            box-shadow: 0 0 0 4px rgba(16, 93, 130, 0.08);
            background: white;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--request-primary) 0%, var(--request-dark) 100%);
            border: none;
            padding: 1.2rem 3rem;
            border-radius: 12px;
            font-weight: 800;
            font-size: 1.1rem;
            box-shadow: 0 10px 20px rgba(16, 93, 130, 0.22);
            transition: 0.3s;
            color: white;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(16, 93, 130, 0.30);
            color: white;
        }

        .invalid-feedback {
            font-size: 0.85rem;
            font-weight: bold;
        }

        .payment-card {
            background: linear-gradient(135deg, #fffaf0 0%, #fff7ed 100%);
            border: 2px solid rgba(184, 144, 71, 0.35);
            border-radius: 16px;
            padding: 1.5rem;
        }
    </style>
@endsection

@section('content')
    <div class="container py-5 request-container">

        <div class="row mb-4">
            <div class="col-12 text-center">
                <h1 class="fw-bold text-primary mb-3">{{ __('app.request_document') }}</h1>
                <p class="text-muted fs-5">
                    {{ app()->getLocale() == 'ar'
        ? 'يرجى تعبئة النموذج أدناه بدقة، وسيتم مراجعة طلبك وإصدار الوثيقة رقمياً أو ورقياً حسب رغبتك.'
        : 'Please fill out the form below carefully. Your request will be reviewed and the document will be issued digitally or physically based on your preference.' }}
                </p>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger rounded-4 shadow-sm mb-4">
                <h6 class="fw-bold mb-2">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ app()->getLocale() == 'ar' ? 'يرجى تصحيح الأخطاء التالية:' : 'Please correct the following errors:' }}
                </h6>
                <ul class="mb-0 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('graduate.documents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-section">

                <div class="mb-5">
                    <h5 class="section-title">
                        <i class="fas fa-file-invoice text-primary"></i>
                        {{ app()->getLocale() == 'ar' ? '1. اختر نوع الوثيقة' : '1. Select Document Type' }}
                    </h5>

                    <div class="row g-4">
                        @foreach($types as $type)
                                        <div class="col-md-6">
                                            <label class="w-100 h-100 m-0">
                                                <input type="radio" name="document_type_id" value="{{ $type->id }}"
                                                    class="btn-check doc-type-radio" required {{ old('document_type_id') == $type->id ? 'checked' : '' }}
                                                    data-payment-required="{{ $type->payment_required ? 'true' : 'false' }}"
                                                    data-fee="{{ $type->fee_amount }}" data-currency="{{ $type->currency }}">

                                                <div class="doc-type-card">
                                                    <div class="icon-circle">
                                                        <i
                                                            class="{{ $type->code === 'ACADEMIC_RECORD' ? 'fas fa-graduation-cap' : 'fas fa-award' }} fa-lg"></i>
                                                    </div>

                                                    <h5 class="fw-bold text-dark">
                                                        {{ app()->getLocale() == 'ar' ? $type->name_ar : $type->name_en }}
                                                    </h5>

                                                    <p class="small text-muted mb-0">
                                                        {{ $type->code === 'ACADEMIC_RECORD'
                            ? (app()->getLocale() == 'ar'
                                ? 'نسخة مفصلة لجميع المقررات والدرجات لكل مستوى دراسي.'
                                : 'A detailed copy of all courses and grades for each academic level.')
                            : (app()->getLocale() == 'ar'
                                ? 'بيان رسمي ملخص للدرجات والتقدير العام والترتيب.'
                                : 'An official summary statement of grades, overall rating, and ranking.') }}
                                                    </p>

                                                    @if($type->payment_required)
                                                        <div class="mt-2 small">
                                                            <span class="badge bg-warning text-dark rounded-pill">
                                                                <i class="fas fa-money-bill-wave me-1"></i>
                                                                {{ number_format($type->fee_amount, 0) }} {{ $type->currency }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <div class="mt-2 small">
                                                            <span class="badge bg-success text-white rounded-pill">
                                                                <i class="fas fa-check-circle me-1"></i>
                                                                {{ app()->getLocale() == 'ar' ? 'مجانية' : 'Free' }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </label>
                                        </div>
                        @endforeach
                    </div>

                    @error('document_type_id')
                        <div class="text-danger mt-2 fw-bold small">{{ $message }}</div>
                    @enderror
                </div>

                <div id="paymentSection" class="mb-5" style="display: none;">
                    <h5 class="section-title">
                        <i class="fas fa-credit-card text-primary"></i>
                        {{ app()->getLocale() == 'ar' ? '4. معلومات الدفع' : '4. Payment Information' }}
                    </h5>

                    <div class="payment-card">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-warning bg-opacity-25 p-3 rounded-circle">
                                        <i class="fas fa-money-bill-wave fa-lg text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="small text-muted fw-bold">{{ __('app.fee_amount') }}</div>
                                        <div class="fw-bold fs-5" id="displayFee">--</div>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <div class="small text-muted fw-bold">{{ __('app.bank_name') }}</div>
                                    <div class="fw-bold">{{ config('payment.bank_name') }}</div>
                                </div>

                                <div class="mb-2">
                                    <div class="small text-muted fw-bold">{{ __('app.account_name') }}</div>
                                    <div class="fw-bold">{{ config('payment.account_name') }}</div>
                                </div>

                                <div class="mb-2">
                                    <div class="small text-muted fw-bold">{{ __('app.account_number') }}</div>
                                    <div class="fw-bold font-monospace">{{ config('payment.account_number') }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div
                                    class="bg-white bg-opacity-75 p-3 rounded-3 border border-warning border-opacity-50 h-100">
                                    <div class="small fw-bold text-muted mb-2">
                                        <i class="fas fa-info-circle text-warning me-1"></i>
                                        {{ __('app.payment_instructions') }}
                                    </div>

                                    <p class="small mb-3">{{ config('payment.instructions') }}</p>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-dark">
                                            {{ __('app.upload_payment_proof') }}
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input type="file" name="payment_proof" class="form-control"
                                            accept=".jpg,.jpeg,.png,.pdf">

                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-info-circle me-1"></i>
                                            {{ app()->getLocale() == 'ar'
        ? 'الصيغ المسموحة: JPG, JPEG, PNG, PDF - الحد الأقصى 5 ميجابايت.'
        : 'Allowed formats: JPG, JPEG, PNG, PDF - Max 5 MB.' }}
                                        </div>

                                        @error('payment_proof')
                                            <div class="text-danger small fw-bold mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="freeDocumentSection" class="mb-5" style="display: none;">
                    <h5 class="section-title">
                        <i class="fas fa-info-circle text-success"></i>
                        {{ app()->getLocale() == 'ar' ? 'معلومات الرسوم' : 'Fee Information' }}
                    </h5>
                    <div class="alert alert-success rounded-4 p-4 d-flex align-items-center gap-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1 text-success">{{ app()->getLocale() == 'ar' ? 'وثيقة مجانية' : 'Free Document' }}</h5>
                            <p class="mb-0 text-muted fs-6">
                                {{ app()->getLocale() == 'ar' 
                                    ? 'هذه الوثيقة مجانية ولا تتطلب رفع إثبات دفع.' 
                                    : 'This document is free and does not require uploading payment proof.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <h5 class="section-title">
                        <i class="fas fa-language text-primary"></i>
                        {{ app()->getLocale() == 'ar' ? '2. خيارات الوثيقة والتسليم' : '2. Document Options & Delivery' }}
                    </h5>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted mb-2">{{ __('app.language') }}</label>
                            <select name="language" class="form-select custom-input" required>
                                <option value="AR" {{ old('language') == 'AR' ? 'selected' : '' }}>
                                    {{ __('app.documents_lang_ar') }}
                                </option>
                                <option value="EN" {{ old('language') == 'EN' ? 'selected' : '' }}>
                                    {{ __('app.documents_lang_en') }}
                                </option>
                            </select>

                            @error('language')
                                <span class="text-danger small fw-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted mb-2">{{ __('app.delivery_type') }}</label>
                            <select name="delivery_type" class="form-select custom-input" required>
                                <option value="DIGITAL_PDF" {{ old('delivery_type') == 'DIGITAL_PDF' ? 'selected' : '' }}>
                                    {{ app()->getLocale() == 'ar' ? 'نسخة رقمية (PDF موثق برمز QR)' : 'Digital PDF (QR-signed)' }}
                                </option>
                                <option value="PICKUP" {{ old('delivery_type') == 'PICKUP' ? 'selected' : '' }}>
                                    {{ app()->getLocale() == 'ar' ? 'استلام يدوي (من مقر الجامعة)' : 'In-person pickup' }}
                                </option>
                            </select>

                            @error('delivery_type')
                                <span class="text-danger small fw-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <h5 class="section-title">
                        <i class="fas fa-info-circle text-primary"></i>
                        {{ app()->getLocale() == 'ar' ? '3. الغرض والملاحظات' : '3. Purpose & Notes' }}
                    </h5>

                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-bold text-muted mb-2">
                                {{ __('app.documents_purpose') }}
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text" name="purpose" class="form-control custom-input" value="{{ old('purpose') }}"
                                placeholder="{{ app()->getLocale() == 'ar'
        ? 'مثال: تقديم لوظيفة، مواصلة الدراسات العليا...'
        : 'e.g. Job application, postgraduate studies...' }}" required>

                            @error('purpose')
                                <span class="text-danger small fw-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div
                                class="bg-light p-3 rounded-3 border border-warning border-opacity-50 mt-3 d-flex gap-3 align-items-start">
                                <i class="fas fa-exclamation-triangle text-warning mt-1"></i>
                                <div class="small text-muted">
                                    <strong>{{ app()->getLocale() == 'ar' ? 'ملاحظة هامة:' : 'Important Note:' }}</strong>
                                    {{ app()->getLocale() == 'ar'
        ? 'إذا كانت الوثيقة تتطلب رسوماً، فيجب رفع إثبات دفع لإكمال الطلب. ستتم مراجعة إثبات الدفع من قبل الإدارة المالية قبل تحويل الطلب للمراجعة الأكاديمية.'
        : 'If the document requires a fee, you must upload payment proof to complete the request. The payment proof will be reviewed by the finance department before the request moves to academic review.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center pt-3 border-top mt-5">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-paper-plane me-2"></i>
                        {{ app()->getLocale() == 'ar' ? 'تأكيد وإرسال الطلب' : 'Submit Request' }}
                    </button>

                    <div class="mt-4">
                        <a href="{{ route('graduate.documents.index') }}" class="text-decoration-none text-secondary">
                            <i class="fas fa-arrow-right me-1"></i>
                            {{ app()->getLocale() == 'ar' ? 'العودة لقائمة طلباتي' : 'Back to my requests' }}
                        </a>
                    </div>
                </div>

            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radios = document.querySelectorAll('.doc-type-radio');
            const paymentSection = document.getElementById('paymentSection');
            const freeDocumentSection = document.getElementById('freeDocumentSection');
            const displayFee = document.getElementById('displayFee');

            function updatePaymentSection() {
                const selected = document.querySelector('.doc-type-radio:checked');

                if (selected) {
                    if (selected.dataset.paymentRequired === 'true') {
                        paymentSection.style.display = 'block';
                        freeDocumentSection.style.display = 'none';
                        displayFee.textContent = Number(selected.dataset.fee).toLocaleString() + ' ' + selected.dataset.currency;
                    } else {
                        paymentSection.style.display = 'none';
                        freeDocumentSection.style.display = 'block';
                    }
                } else {
                    paymentSection.style.display = 'none';
                    freeDocumentSection.style.display = 'none';
                }
            }

            radios.forEach(r => r.addEventListener('change', updatePaymentSection));
            updatePaymentSection();
        });
    </script>
@endsection