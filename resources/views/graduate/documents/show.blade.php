@extends('layouts.app')

@section('title', __('app.documents_request_details'))

@section('styles')
<style>
    :root {
        --primary-blue: #1a237e;
        --secondary-blue: #0d47a1;
    }
    body { background-color: #f8fafc; }
    
    .detail-card {
        background: white;
        border-radius: 1rem;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    
    .timeline-container {
        position: relative;
        padding-right: 2rem;
        margin-top: 1rem;
    }
    
    .timeline-container::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        right: 0.5rem;
        width: 2px;
        background: #e2e8f0;
        border-radius: 2px;
    }

    .timeline-step {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .timeline-icon {
        position: absolute;
        right: -2.3rem;
        top: 0;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        border: 4px solid white;
        background: white;
        z-index: 2;
    }

    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 0.3rem;
    }
    
    .info-value {
        font-size: 1.05rem;
        font-weight: 600;
        color: #1e293b;
    }

    .status-banner {
        border-radius: 1rem;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
</style>
@endsection

@section('content')
@php
    $statusThemes = [
        'SUBMITTED' => ['bg' => 'bg-info bg-opacity-10 text-info', 'border' => 'border-info', 'icon' => 'fa-paper-plane'],
        'UNDER_REVIEW' => ['bg' => 'bg-warning bg-opacity-10 text-warning', 'border' => 'border-warning', 'icon' => 'fa-hourglass-half'],
        'APPROVED' => ['bg' => 'bg-success bg-opacity-10 text-success', 'border' => 'border-success', 'icon' => 'fa-check'],
        'REJECTED' => ['bg' => 'bg-danger bg-opacity-10 text-danger', 'border' => 'border-danger', 'icon' => 'fa-times'],
        'READY' => ['bg' => 'bg-primary bg-opacity-10 text-primary', 'border' => 'border-primary', 'icon' => 'fa-box'],
        'ISSUED' => ['bg' => 'bg-secondary bg-opacity-10 text-secondary', 'border' => 'border-secondary', 'icon' => 'fa-file-contract'],
    ];
    $theme = $statusThemes[$document->status] ?? ['bg' => 'bg-light text-dark', 'border' => 'border-dark', 'icon' => 'fa-circle'];
    $pdfOk = $document->issuedDocument && \Illuminate\Support\Facades\Storage::disk('public')->exists($document->issuedDocument->pdf_path);
    $canDownload = in_array($document->status, ['READY', 'ISSUED'], true) && $pdfOk;
@endphp

<div class="container py-5">
    
    <!-- Top Nav -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('graduate.documents.index') }}" class="text-decoration-none fw-bold"><i class="fas fa-home me-1"></i> طلباتي</a></li>
                    <li class="breadcrumb-item active" aria-current="page">تفاصيل الطلب</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-primary mb-0">الطلب #{{ $document->tracking_code }}</h3>
        </div>
        <a href="{{ route('graduate.documents.index') }}" class="btn btn-light shadow-sm text-secondary rounded-pill px-4 fw-bold">
            العودة للقائمة <i class="fas fa-arrow-left ms-1"></i>
        </a>
    </div>

    <!-- Status Banner -->
    <div class="status-banner border {{ $theme['border'] }} border-opacity-25 {{ $theme['bg'] }}">
        <div class="bg-white rounded-circle p-3 shadow-sm d-flex align-items-center justify-content-center" style="width:60px;height:60px;">
            <i class="fas {{ $theme['icon'] }} fa-xl"></i>
        </div>
        <div>
            <div class="small fw-bold mb-1 opacity-75">الحالة الحالية:</div>
            <h4 class="fw-bold mb-0">{{ __('app.document_status.'.$document->status) }}</h4>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Details Box -->
        <div class="col-lg-8">
            <div class="detail-card p-4 p-md-5 h-100">
                <h5 class="fw-bold border-bottom pb-3 mb-4 text-primary"><i class="fas fa-file-alt me-2"></i> معلومات الوثيقة المدخلة</h5>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-label">نوع الوثيقة</div>
                        <div class="info-value d-flex align-items-center gap-2">
                            <i class="fas {{ $document->documentType->code === 'ACADEMIC_RECORD' ? 'fa-graduation-cap' : 'fa-award' }} text-secondary"></i>
                            {{ app()->getLocale() === 'ar' ? $document->documentType->name_ar : $document->documentType->name_en }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">رمز التتبع المرجعي</div>
                        <div class="info-value font-monospace text-primary bg-light px-2 py-1 rounded d-inline-block">{{ $document->tracking_code }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">لغة الوثيقة</div>
                        <div class="info-value">
                            <i class="fas fa-language text-secondary me-1"></i>
                            {{ $document->language === 'AR' ? 'اللغة العربية (AR)' : 'اللغة الإنجليزية (EN)' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">طريقة التسليم المطلوبة</div>
                        <div class="info-value">
                            <i class="fas {{ $document->delivery_type === 'DIGITAL_PDF' ? 'fa-cloud-download-alt text-success' : 'fa-hand-holding text-warning' }} me-1"></i>
                            {{ $document->delivery_type === 'DIGITAL_PDF' ? 'نسخة رقمية (PDF)' : 'استلام يدوي' }}
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="info-label">الغرض من الطلب</div>
                        <div class="bg-light p-3 rounded-3 text-dark mt-1">{{ $document->purpose }}</div>
                    </div>
                </div>

                @if($document->admin_note)
                    <div class="mt-4 alert alert-warning border-warning border-opacity-50 shadow-sm rounded-3">
                        <div class="fw-bold mb-2 text-dark"><i class="fas fa-comment-dots text-warning me-2"></i> رسالة من الإدارة:</div>
                        <p class="mb-0 text-dark">{{ $document->admin_note }}</p>
                    </div>
                @endif
                
                <!-- Action Area -->
                <div class="border-top pt-4 mt-5 d-flex justify-content-center">
                    @if($canDownload)
                        <div class="text-center w-100">
                            <h5 class="fw-bold text-success mb-3">ملف الوثيقة جاهز للتحميل</h5>
                            <a href="{{ route('graduate.documents.download', $document) }}" class="btn btn-success btn-lg px-5 py-3 rounded-pill shadow fw-bold">
                                <i class="fas fa-download me-2"></i> تحميل الوثيقة (PDF)
                            </a>
                            <p class="small text-muted mt-3 mb-0">يمكنك إجراء التحميل في أي وقت. الوثيقة مزودة برمز استجابة سريعة (QR) موثق.</p>
                        </div>
                    @elseif(in_array($document->status, ['READY', 'ISSUED'], true))
                         <div class="alert alert-danger border-0 w-100 text-center rounded-3">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <h6 class="fw-bold">ملف الوثيقة غير موجود على الخادم!</h6>
                            <p class="mb-0 small text-muted">يرجى الاتصال بالدعم الفني والمطالبة بإعادة إصدار ملف الوثيقة.</p>
                        </div>
                    @elseif($document->status === 'REJECTED')
                        <div class="alert alert-secondary w-100 text-center rounded-3 border-0">
                            <i class="fas fa-ban fa-2x text-muted mb-2"></i>
                            <h6 class="fw-bold mb-0">لا يمكن تحميل وثيقة مرفوضة.</h6>
                        </div>
                    @else
                        <div class="text-center w-100 p-4 bg-light rounded-3">
                            <i class="fas fa-lock fa-3x text-muted opacity-25 mb-3"></i>
                            <h6 class="fw-bold text-muted mb-2">الوثيقة قيد التنفيذ</h6>
                            <p class="mb-0 small text-muted">سيظهر زر التحميل هنا فور جاهزية الملف.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Timeline Log Box -->
        <div class="col-lg-4">
            <div class="detail-card p-4 h-100">
                <h5 class="fw-bold border-bottom pb-3 mb-4 text-primary"><i class="fas fa-history me-2"></i> مسار الطلب</h5>
                
                <div class="timeline-container pe-2">
                    <!-- Submission Record -->
                    <div class="timeline-step">
                        <div class="timeline-icon text-info border-info"><i class="fas fa-paper-plane"></i></div>
                        <div class="bg-light rounded-3 p-3 ms-2">
                            <h6 class="fw-bold text-dark mb-1 d-flex justify-content-between">
                                تم تقديم الطلب
                                <span class="badge bg-info bg-opacity-25 text-info small align-self-start ms-2">SUBMITTED</span>
                            </h6>
                            <div class="small text-muted"><i class="far fa-clock me-1"></i> {{ $document->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>

                    <!-- DB logs -->
                    @foreach($document->logs as $log)
                        @php
                            $logConf = $statusThemes[$log->to_status] ?? ['border' => 'border-secondary', 'icon' => 'fa-check', 'bg' => 'text-secondary'];
                        @endphp
                        <div class="timeline-step">
                            <div class="timeline-icon {{ $logConf['bg'] }} {{ $logConf['border'] }}"><i class="fas {{ $logConf['icon'] }}"></i></div>
                            <div class="bg-light rounded-3 p-3 ms-2">
                                <h6 class="fw-bold text-dark mb-1 d-flex justify-content-between align-items-center">
                                    {{ __('app.document_status.'.$log->to_status) }}
                                    <span class="badge {{ $logConf['border'] }} border text-dark ms-2" style="font-size:0.65rem;">
                                        تحديث
                                    </span>
                                </h6>
                                <div class="small text-muted mb-2"><i class="far fa-clock me-1"></i> {{ $log->created_at->format('Y-m-d H:i') }}</div>
                                
                                <div class="p-2 bg-white rounded border border-light small">
                                    <span class="text-primary fw-bold"><i class="fas fa-user-shield me-1"></i> الإدارة:</span>
                                    {{ $log->admin?->name ?? 'نظام الموافقة' }}
                                    @if($log->note)
                                        <hr class="my-1">
                                        <span class="text-secondary fst-italic">"{{ $log->note }}"</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
            </div>
        </div>
    </div>
    
</div>
@endsection
