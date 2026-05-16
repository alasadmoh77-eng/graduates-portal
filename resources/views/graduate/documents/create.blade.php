@extends('layouts.app')

@section('title', 'طلب وثيقة رسمية')

@section('styles')
<style>
    :root {
        --primary-blue: #1a237e;
        --secondary-blue: #0d47a1;
        --accent-gold: #d4af37;
    }

    .request-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .form-section {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.02);
    }

    .section-title {
        font-weight: 800;
        color: var(--primary-blue);
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

    .btn-check:checked + .doc-type-card {
        border-color: var(--primary-blue);
        background-color: #eef2ff;
        box-shadow: 0 10px 20px rgba(26, 35, 126, 0.1);
    }

    .btn-check:checked + .doc-type-card::after {
        content: '\f058';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        top: 15px;
        left: 15px;
        color: var(--primary-blue);
        font-size: 1.25rem;
    }

    .doc-type-card:hover {
        border-color: var(--secondary-blue);
        background-color: #f8faff;
        transform: translateY(-2px);
    }

    .icon-circle {
        width: 50px;
        height: 50px;
        background: rgba(26, 35, 126, 0.05);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        color: var(--primary-blue);
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
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 4px rgba(26, 35, 126, 0.05);
        background: white;
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        border: none;
        padding: 1.2rem 3rem;
        border-radius: 12px;
        font-weight: 800;
        font-size: 1.1rem;
        box-shadow: 0 10px 20px rgba(26, 35, 126, 0.2);
        transition: 0.3s;
        color: white;
    }

    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(26, 35, 126, 0.3);
        color: white;
    }
    
    .invalid-feedback {
        font-size: 0.85rem;
        font-weight: bold;
    }
</style>
@endsection

@section('content')
<div class="container py-5 request-container">
    
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="fw-bold text-primary mb-3">طلب وثيقة رسمية</h1>
            <p class="text-muted fs-5">يرجى تعبئة النموذج أدناه بدقة، وسيتم مراجعة طلبك وإصدار الوثيقة رقمياً أو ورقياً حسب رغبتك.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm mb-4">
            <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i> يرجى تصحيح الأخطاء التالية:</h6>
            <ul class="mb-0 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('graduate.documents.store') }}" method="POST">
        @csrf
        <div class="form-section">
            
            <!-- Document Type Selection -->
            <div class="mb-5">
                <h5 class="section-title"><i class="fas fa-file-invoice text-primary"></i> 1. اختر نوع الوثيقة</h5>
                <div class="row g-4">
                    @foreach($types as $type)
                        <div class="col-md-6">
                            <label class="w-100 h-100 m-0">
                                <input type="radio" name="document_type_id" value="{{ $type->id }}" class="btn-check" required {{ old('document_type_id') == $type->id ? 'checked' : '' }}>
                                <div class="doc-type-card">
                                    <div class="icon-circle">
                                        <i class="{{ $type->code === 'ACADEMIC_RECORD' ? 'fas fa-graduation-cap' : 'fas fa-award' }} fa-lg"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark">{{ $type->name_ar }}</h5>
                                    <p class="small text-muted mb-0">
                                        {{ $type->code === 'ACADEMIC_RECORD' 
                                            ? 'نسخة مفصلة لجميع المقررات والدرجات لكل مستوى دراسي.' 
                                            : 'بيان رسمي ملخص للدرجات والتقدير العام والترتيب.' }}
                                    </p>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('document_type_id') <div class="text-danger mt-2 fw-bold small">{{ $message }}</div> @enderror
            </div>

            <!-- Language & Delivery Details -->
            <div class="mb-5">
                <h5 class="section-title"><i class="fas fa-language text-primary"></i> 2. خيارات الوثيقة والتسليم</h5>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted mb-2">لغة الوثيقة</label>
                        <select name="language" class="form-select custom-input" required>
                            <option value="AR" {{ old('language') == 'AR' ? 'selected' : '' }}>اللغة العربية</option>
                            <option value="EN" {{ old('language') == 'EN' ? 'selected' : '' }}>اللغة الإنجليزية</option>
                        </select>
                        @error('language') <span class="text-danger small fw-bold">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted mb-2">طريقة التسليم</label>
                        <select name="delivery_type" class="form-select custom-input" required>
                            <option value="DIGITAL_PDF" {{ old('delivery_type') == 'DIGITAL_PDF' ? 'selected' : '' }}>نسخة رقمية (PDF موثق برمز QR)</option>
                            <option value="PICKUP" {{ old('delivery_type') == 'PICKUP' ? 'selected' : '' }}>استلام يدوي (من مقر الجامعة)</option>
                        </select>
                        @error('delivery_type') <span class="text-danger small fw-bold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Request Purpose -->
            <div class="mb-5">
                <h5 class="section-title"><i class="fas fa-info-circle text-primary"></i> 3. الغرض والملاحظات</h5>
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label fw-bold text-muted mb-2">الغرض من استخراج الوثيقة (مطلوب)</label>
                        <input type="text" name="purpose" class="form-control custom-input" value="{{ old('purpose') }}" placeholder="مثال: تقديم لوظيفة، مواصلة الدراسات العليا..." required>
                        @error('purpose') <span class="text-danger small fw-bold">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-12">
                        <div class="bg-light p-3 rounded-3 border border-warning border-opacity-50 mt-3 d-flex gap-3 align-items-start">
                            <i class="fas fa-exclamation-triangle text-warning mt-1"></i>
                            <div class="small text-muted">
                                <strong>ملاحظة هامة:</strong> يتوجب دفع الرسوم المستحقة للوثيقة. ستظهر حالة الطلب كـ "قيد المراجعة" بعد التقديم وسيتم إصدارها بعد الاعتماد الإداري. النسخ الرقمية تكون متاحة للتحميل مباشرة.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submission Area -->
            <div class="text-center pt-3 border-top mt-5">
                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-paper-plane me-2"></i> تأكيد وإرسال الطلب
                </button>
                <div class="mt-4">
                    <a href="{{ route('graduate.documents.index') }}" class="text-decoration-none text-secondary">
                        <i class="fas fa-arrow-right me-1"></i> العودة لقائمة طلباتي
                    </a>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
