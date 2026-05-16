@extends('layouts.app')

@section('title', __('app.admin_request_title', ['code' => $documentRequest->tracking_code]))


@section('content')
@php
    $statusThemes = [
        'SUBMITTED' => ['bg' => 'bg-info bg-opacity-10 text-info', 'border' => 'border-info', 'icon' => 'fa-paper-plane'],
        'UNDER_REVIEW' => ['bg' => 'bg-warning bg-opacity-10 text-warning', 'border' => 'border-warning', 'icon' => 'fa-hourglass-half'],
        'APPROVED' => ['bg' => 'bg-success bg-opacity-10 text-success', 'border' => 'border-success', 'icon' => 'fa-check-double'],
        'REJECTED' => ['bg' => 'bg-danger bg-opacity-10 text-danger', 'border' => 'border-danger', 'icon' => 'fa-times'],
        'READY' => ['bg' => 'bg-primary bg-opacity-10 text-primary', 'border' => 'border-primary', 'icon' => 'fa-box'],
        'ISSUED' => ['bg' => 'bg-secondary bg-opacity-10 text-secondary', 'border' => 'border-secondary', 'icon' => 'fa-file-pdf'],
    ];
    $theme = $statusThemes[$documentRequest->status] ?? ['bg' => 'bg-light text-dark', 'border' => 'border-dark', 'icon' => 'fa-circle'];
@endphp

<div class="container-fluid py-4 px-lg-5">
    
    <!-- Top Nav Map -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none fw-bold"><i class="fas fa-desktop me-1"></i> الإدارة</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.requests.index') }}" class="text-decoration-none fw-bold">الطلبات</a></li>
                    <li class="breadcrumb-item active" aria-current="page">الطلب #{{ $documentRequest->tracking_code }}</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-primary mb-0">لوحة التحكم بالطلب</h2>
        </div>
        <a href="{{ route('admin.requests.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
            <i class="fas fa-arrow-right me-2"></i> {{ __('app.admin_back_to_requests') }}
        </a>
    </div>

    {{-- Workflow Progress Bar --}}
    <x-section-card class="mb-4">
        <x-workflow-progress :currentStatus="$documentRequest->status" />
    </x-section-card>

    @if($documentRequest->admin_note)
        <div class="alert alert-warning border-warning border-opacity-50 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3">
            <i class="fas fa-exclamation-circle fa-2x text-warning"></i>
            <div>
                <h6 class="fw-bold mb-1 text-dark">ملاحظة إدارية مسجلة مسبقاً</h6>
                <p class="mb-0 text-dark">{{ $documentRequest->admin_note }}</p>
            </div>
        </div>
    @endif

    <div class="row g-4">
        
        <!-- Right Column: Info -->
        <div class="col-lg-8">
            
            <!-- Request Header Banner -->
            <div class="admin-card mb-4 bg-white">
                <div class="p-4 d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas {{ $documentRequest->documentType->code === 'ACADEMIC_RECORD' ? 'fa-graduation-cap' : 'fa-award' }} fa-xl"></i>
                        </div>
                        <div>
                            <div class="small fw-bold text-muted mb-1">نوع الوثيقة المطلوبة:</div>
                            <h4 class="fw-bold text-dark mb-0">{{ $documentRequest->documentType->name_ar }}</h4>
                        </div>
                    </div>
                    <div>
                        <div class="small fw-bold text-muted mb-1">حالة الطلب:</div>
                        <x-status-badge :status="$documentRequest->status" />
                    </div>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="info-section">
                                <div class="info-label">رمز التتبع</div>
                                <div class="info-value font-monospace text-primary">{{ $documentRequest->tracking_code }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-section">
                                <div class="info-label">لغة الوثيقة</div>
                                <div class="info-value">{{ $documentRequest->language === 'AR' ? 'العربية' : 'الأنكليزية' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-section">
                                <div class="info-label">طريقة التسليم</div>
                                <div class="info-value">{{ $documentRequest->delivery_type === 'DIGITAL_PDF' ? 'نسخة رقمية' : 'استلام يدوي' }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-section">
                                <div class="info-label">الغرض من استخراج الوثيقة</div>
                                <div class="info-value">{{ $documentRequest->purpose }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graduate Info -->
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h5 class="fw-bold text-secondary mb-0"><i class="fas fa-user-graduate me-2"></i> بيانات الخريج</h5>
                </div>
                <div class="p-4 bg-white">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-label">الاسم الرباعي</div>
                            <div class="info-value d-flex align-items-center gap-2">
                                <i class="fas fa-user-circle text-muted"></i>
                                {{ $documentRequest->user->name }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">الرقم الجامعي</div>
                            <div class="info-value">{{ $documentRequest->user->graduate->university_id ?? 'غير متوفر' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">التخصص والكلية</div>
                            <div class="info-value">{{ $documentRequest->user->graduate->major->name_ar ?? 'غير متوفر' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">سنة التخرج</div>
                            <div class="info-value">{{ $documentRequest->user->graduate->graduation_year ?? 'غير متوفر' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">البريد الإلكتروني للاتصال</div>
                            <div class="info-value"><a href="mailto:{{ $documentRequest->user->email }}" class="text-decoration-none">{{ $documentRequest->user->email }}</a></div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">رقم الهاتف</div>
                            <div class="info-value" dir="ltr">{{ $documentRequest->user->graduate->phone ?? 'غير متوفر' }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Left Column: Actions -->
        <div class="col-lg-4">
            
            @php
                $isAcademicDoc = in_array($documentRequest->documentType->code, ['ACADEMIC_RECORD', 'GRADES_CERTIFICATE']);
            @endphp
            @if($isAcademicDoc)
                <div class="admin-card mb-4 border-warning border-opacity-50">
                    <div class="admin-card-header bg-warning bg-opacity-10">
                        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-database me-2"></i> إدارة السجل الأكاديمي</h5>
                    </div>
                    <div class="p-4 bg-white">
                        @if(!($hasAcademicRecordData ?? true))
                            <p class="small text-danger fw-bold mb-2">{{ __('app.academic_record_missing_title') }}</p>
                            <p class="small text-muted mb-3">{{ __('app.academic_record_missing_hint') }}</p>
                            <a href="{{ route('admin.graduates.academic-record.edit', $documentRequest->user) }}" class="btn btn-warning rounded-pill fw-bold w-100 mb-2">
                                <i class="fas fa-plus-circle me-2"></i> إدخال السجل الأكاديمي
                            </a>
                        @else
                            <p class="small text-muted mb-2 fw-bold">تعديل بيانات المعدل والمقررات</p>
                            <p class="small text-muted mb-3">إذا وجدت خطأ في البيانات، عدّل السجل الأكاديمي ثم أعد إنشاء الوثيقة من هذه الصفحة.</p>
                            <div class="d-flex flex-column gap-2 mb-3">
                                <span class="badge bg-light text-dark border text-start p-2"><i class="fas fa-edit text-primary me-2"></i>1. تعديل السجل الأكاديمي وحفظه</span>
                                <span class="badge bg-light text-dark border text-start p-2"><i class="fas fa-arrow-right text-primary me-2"></i>2. العودة لتفاصيل الطلب بالخلف</span>
                                <span class="badge bg-light text-dark border text-start p-2"><i class="fas fa-sync text-primary me-2"></i>3. النقر على مفتاح "إعادة إنشاء الوثيقة"</span>
                            </div>
                            <a href="{{ route('admin.graduates.academic-record.edit', $documentRequest->user) }}" class="btn btn-outline-warning text-dark border-warning rounded-pill fw-bold w-100">
                                <i class="fas fa-edit me-2"></i> تعديل السجل الأكاديمي
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Issued Document Generation Box -->
            @if(in_array($documentRequest->status, ['APPROVED', 'READY'], true))
            <div class="admin-card mb-4 border-success border-opacity-25">
                <div class="admin-card-header bg-success bg-opacity-10 border-success border-opacity-25">
                    <h5 class="fw-bold text-success mb-0"><i class="fas fa-file-pdf me-2"></i> إصدار الوثيقة (PDF)</h5>
                </div>
                <div class="p-4 bg-white text-center">
                    <p class="small text-muted mb-4">تمت الموافقة على الطلب وهو جاهز لإصدار الشهادة الرقمية المولدة بالنظام.</p>
                    
                    @php
                        $canGenerateAcademicPdf = !in_array($documentRequest->documentType->code, ['ACADEMIC_RECORD', 'GRADES_CERTIFICATE'])
                            || ($hasAcademicRecordData ?? false);
                    @endphp
                    <form action="{{ route('admin.requests.generate-pdf', $documentRequest) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 py-3 rounded-pill shadow-sm fw-bold mb-3" @if(!$canGenerateAcademicPdf) disabled @endif>
                            <i class="fas fa-cogs me-2"></i>
                            {{ $documentRequest->status === 'READY' ? 'إعادة إنشاء الوثيقة (Regenerate)' : 'إنشاء وحفظ الوثيقة' }}
                        </button>
                    </form>
                    @if(!$canGenerateAcademicPdf)
                        <p class="small text-danger mb-0">{{ __('app.academic_record_missing_title') }} — {{ __('app.academic_record_open_entry') }}</p>
                    @endif

                    @if($documentRequest->issuedDocument)
                        <div class="border-top pt-3 mt-3 text-start">
                            <div class="small fw-bold text-muted mb-2">الملف المنشأ حالياً:</div>
                            <div class="bg-light p-3 rounded-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small fw-bold text-dark font-monospace mb-1">{{ $documentRequest->issuedDocument->serial_number }}</div>
                                    <div style="font-size:0.7rem;" class="text-muted">تم الانشاء: {{ $documentRequest->issuedDocument->issued_at->format('Y-m-d') }}</div>
                                </div>
                                <a href="{{ asset('storage/' . $documentRequest->issuedDocument->pdf_path) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="المعاينة والطباعة">
                                    <i class="fas fa-external-link-alt"></i> فتح
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Status Transition Controller -->
            <div class="admin-card mb-4 bg-white border-primary border-opacity-25">
                <div class="admin-card-header bg-primary bg-opacity-10 border-primary border-opacity-25">
                    <h5 class="fw-bold text-primary mb-0"><i class="fas fa-exchange-alt me-2"></i> إجراءات معالجة الطلب</h5>
                </div>
                <div class="p-4 bg-white">
                    <form action="{{ route('admin.requests.status', $documentRequest) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">الخطوة القادمة (تحديث الحالة):</label>
                            @if(count($availableTransitions) > 0)
                                <div class="d-flex flex-column gap-2">
                                    @foreach($availableTransitions as $status)
                                        <button type="submit" name="status" value="{{ $status }}" class="btn btn-outline-primary transition-btn border-2 rounded-3">
                                            <span>نقل الحالة إلى {{ __('app.document_status.'.$status) }}</span>
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-secondary border-0 text-center py-3 mb-0 rounded-3">
                                    <i class="fas fa-lock text-muted mb-2 fa-2x"></i>
                                    <div class="small fw-bold text-muted">لا توجد حالات نقل متاحة حالياً.</div>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">إضافة ملاحظة إدارية للطالب (اختياري)</label>
                            <textarea name="note" class="form-control bg-light border-0 rounded-3" rows="3" placeholder="اكتب سبب الرفض أو تعليمات المراجعة..."></textarea>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Timeline -->
            <div class="admin-card bg-white mb-4">
                <div class="admin-card-header">
                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-history text-muted me-2"></i> سجل مسار الطلب</h6>
                </div>
                <div class="p-4">
                    <div class="timeline-container pe-2">
                        <!-- Submission -->
                        <div class="timeline-step">
                            <div class="timeline-icon text-info border-info"><i class="fas fa-paper-plane"></i></div>
                            <div class="bg-light rounded-3 p-3 ms-2">
                                <h6 class="fw-bold text-dark mb-1 d-flex justify-content-between align-items-center">
                                    تقديم الطلب عبر البوابة
                                </h6>
                                <div class="small text-muted mb-1">{{ $documentRequest->created_at->format('Y-m-d H:i') }}</div>
                            </div>
                        </div>

                        <!-- DB Activity logs -->
                        @foreach($documentRequest->logs as $log)
                            @php
                                $logConf = $statusThemes[$log->to_status] ?? ['border' => 'border-secondary', 'icon' => 'fa-check', 'bg' => 'text-secondary'];
                            @endphp
                            <div class="timeline-step">
                                <div class="timeline-icon {{ $logConf['bg'] }} {{ $logConf['border'] }}"><i class="fas {{ $logConf['icon'] }}"></i></div>
                                <div class="bg-light rounded-3 p-3 ms-2">
                                    <h6 class="fw-bold text-dark mb-1">
                                        تحديث إلى {{ __('app.document_status.'.$log->to_status) }}
                                    </h6>
                                    <div class="small text-muted mb-2"><i class="far fa-clock me-1"></i> {{ $log->created_at->format('Y-m-d H:i') }}</div>
                                    
                                    <div class="p-2 bg-white rounded border border-light small">
                                        <span class="text-primary fw-bold">المنفذ:</span> {{ $log->admin?->name ?? 'النظام الآلي' }}
                                        @if($log->note)
                                            <hr class="my-1">
                                            <span class="text-dark fst-italic">"{{ $log->note }}"</span>
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
</div>
@endsection
