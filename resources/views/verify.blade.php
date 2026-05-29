@extends('layouts.app')

@section('title', 'تحقق من صحة المستند | Verify Document')

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-md-8">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-header bg-dark text-white p-4 text-center border-0">
                <h3 class="fw-bold mb-0">نظام التحقق الرقمي</h3>
                <p class="mb-0 opacity-75 small">تحقق من صحة المستندات الصادرة عن جامعة إقليم سبأ</p>
            </div>
            <div class="card-body p-4 p-md-5">
                <!-- Search Form -->
                <form action="{{ route('verify.process') }}" method="POST" class="mb-5">
                    @csrf
                    <div class="input-group input-group-lg shadow-sm">
                        <input type="text" name="token" class="form-control border-primary" placeholder="أدخل كود التتبع أو الرقم التسلسلي..." value="{{ $token ?? '' }}" required>
                        <button class="btn btn-primary px-4" type="submit">
                            <i class="fas fa-search me-2"></i> تحقق الآن
                        </button>
                    </div>
                </form>

                @if(isset($document) && $document->is_valid)
                    <!-- VALID DOCUMENT RESULT -->
                    <div class="alert alert-success border-0 shadow-sm p-4 rounded-4 mb-0 animate__animated animate__fadeIn">
                        <div class="text-center mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            <h2 class="fw-bold mt-2">مستند صالح وصحيح</h2>
                            <p class="text-muted">تم التحقق من صحة المستند بنجاح</p>
                        </div>
                        
                        <hr class="opacity-10 mb-4">
                        
                        <div class="row g-4 text-start" dir="rtl">
                            <div class="col-md-6">
                                <label class="small text-muted d-block">اسم الخريج</label>
                                <span class="fw-bold fs-5 text-dark">{{ $document->documentRequest->user->name }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted d-block">الرقم الجامعي (مخفي)</label>
                                @php
                                    $uid = $document->documentRequest->user->graduate->university_id;
                                    $maskedId = substr($uid, 0, 3) . '****' . substr($uid, -2);
                                @endphp
                                <span class="fw-bold fs-5 text-dark">{{ $maskedId }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted d-block">نوع الوثيقة</label>
                                <span class="fw-bold fs-5 text-dark">{{ $document->documentRequest->documentType->name_ar }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted d-block">التخصص</label>
                                <span class="fw-bold fs-5 text-dark">{{ $document->documentRequest->user->graduate->major->name_ar }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted d-block">الرقم التسلسلي</label>
                                <span class="text-primary fw-bold fs-5">{{ $document->serial_number }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted d-block">تاريخ الإصدار</label>
                                <span class="fw-bold fs-5 text-dark">{{ $document->issued_at->format('Y-m-d') }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted d-block">حالة الوثيقة</label>
                                <span class="badge bg-success fs-6">صالحة وموثقة</span>
                            </div>
                        </div>
                    </div>
                @elseif(isset($token))
                    <!-- NOT FOUND OR INVALID RESULT -->
                    <div class="alert alert-danger border-0 shadow-sm p-5 rounded-4 text-center animate__animated animate__shakeX">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                        <h2 class="fw-bold mt-3 text-danger">الوثيقة غير موجودة أو غير صالحة</h2>
                        <p class="mb-0 mt-2 text-muted">لم نتمكن من العثور على وثيقة صالحة تطابق البيانات المدخلة.</p>
                    </div>
                @endif
            </div>
            <div class="card-footer bg-light p-4 text-center border-0">
                <p class="mb-0 small text-muted">
                    <i class="fas fa-lock me-1"></i> يتم تأمين جميع عمليات التحقق عبر التشفير الرقمي
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
