@extends('layouts.app')

@section('title', 'تتبع طلب التوظيف | بوابة الخريجين')

@section('styles')
<style>
    .timeline-steps {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        margin-top: 2rem;
        margin-bottom: 2rem;
    }
    .timeline-steps::after {
        content: "";
        position: absolute;
        height: 4px;
        background-color: #e9ecef;
        width: 100%;
        top: 20px;
        z-index: 1;
    }
    .timeline-step-progress {
        position: absolute;
        height: 4px;
        background-color: #198754;
        top: 20px;
        z-index: 2;
        transition: width 0.5s ease;
    }
    .timeline-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 3;
        width: 25%;
    }
    .timeline-step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #fff;
        border: 4px solid #e9ecef;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
        transition: all 0.3s ease;
        color: #6c757d;
    }
    .timeline-step.active .timeline-step-circle {
        border-color: #0d6efd;
        color: #0d6efd;
        box-shadow: 0 0 10px rgba(13, 110, 253, 0.3);
    }
    .timeline-step.completed .timeline-step-circle {
        border-color: #198754;
        background-color: #198754;
        color: #fff;
    }
    .timeline-step.rejected-step .timeline-step-circle {
        border-color: #dc3545;
        background-color: #dc3545;
        color: #fff;
    }
    .timeline-step-label {
        margin-top: 10px;
        font-size: 0.85rem;
        font-weight: bold;
        color: #6c757d;
        text-align: center;
    }
    .timeline-step.active .timeline-step-label {
        color: #0d6efd;
    }
    .timeline-step.completed .timeline-step-label {
        color: #198754;
    }
    .timeline-step.rejected-step .timeline-step-label {
        color: #dc3545;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('graduate.applications.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-right me-1"></i> العودة لقائمة طلباتي
        </a>
    </div>

    <!-- Main Alert/Status Banner -->
    @if($application->status === 'hired')
        <div class="alert alert-success border-0 shadow-lg rounded-4 p-4 mb-4 text-center text-md-start" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); color: #155724;">
            <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    <i class="fas fa-trophy fa-4x text-success"></i>
                </div>
                <div class="col-md-10">
                    <h4 class="fw-bold mb-1">تهانينا الحارة! تم قبولك للوظيفة!</h4>
                    <p class="mb-0">يسرنا إعلامك بأنه قد تم اختيارك من قبل الجهة الموظفة لشغل منصب <strong>{{ $application->job->title }}</strong>. سيتواصل معك ممثل الشركة قريباً لاستكمال إجراءات التوظيف وعقد العمل.</p>
                </div>
            </div>
        </div>
    @elseif($application->status === 'rejected')
        <div class="alert alert-danger border-0 shadow-lg rounded-4 p-4 mb-4 text-center text-md-start" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); color: #721c24;">
            <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    <i class="fas fa-times-circle fa-4x text-danger"></i>
                </div>
                <div class="col-md-10">
                    <h4 class="fw-bold mb-1">تحديث بخصوص طلب التقديم</h4>
                    <p class="mb-0">نشكرك على اهتمامك ووقتك في التقديم لوظيفة <strong>{{ $application->job->title }}</strong>. نأسف لإبلاغك بأن الشركة قد اختارت مرشحين آخرين يتناسبون بشكل أكبر مع متطلبات الوظيفة الحالية. نتمنى لك كل التوفيق في فرصك القادمة.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Tracking Content -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-gradient-primary text-white p-4" style="background: linear-gradient(135deg, #0f2027 0%, #203a43 100%);">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <span class="badge bg-light text-dark mb-2 px-3 py-1.5 rounded-pill fw-bold">حالة الطلب الحالية</span>
                            <h2 class="h3 fw-bold mb-1">{{ $application->job->title }}</h2>
                            <p class="mb-0 opacity-75">الشركة: <span class="fw-bold text-warning">{{ $application->job->company->company_name ?? 'شركة غير معروفة' }}</span></p>
                        </div>
                        <div>
                            <span class="badge bg-{{ $application->statusBadge() }} px-4 py-3 rounded-pill text-white fs-6 shadow-sm">
                                {{ $application->statusLabel() }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-primary text-center">مراحل سير الطلب</h5>

                    <!-- Pipeline Progress Stepper -->
                    @php
                        $statusIndex = match($application->status) {
                            'new' => 0,
                            'shortlisted' => 1,
                            'interviewed' => 2,
                            'hired' => 3,
                            'rejected' => 3,
                            default => 0
                        };
                        $isRejected = $application->status === 'rejected';
                    @endphp

                    <div class="timeline-steps">
                        <!-- Progress Line -->
                        <div class="timeline-step-progress" style="width: {{ $statusIndex * 33.33 }}%;"></div>
                        
                        <!-- Step 1: New -->
                        <div class="timeline-step {{ $statusIndex >= 0 ? ($statusIndex > 0 ? 'completed' : 'active') : '' }}">
                            <div class="timeline-step-circle">
                                @if($statusIndex > 0) <i class="fas fa-check"></i> @else 1 @endif
                            </div>
                            <div class="timeline-step-label">تم التقديم</div>
                        </div>

                        <!-- Step 2: Shortlisted -->
                        <div class="timeline-step {{ $statusIndex >= 1 ? ($statusIndex > 1 ? 'completed' : 'active') : '' }}">
                            <div class="timeline-step-circle">
                                @if($statusIndex > 1) <i class="fas fa-check"></i> @else 2 @endif
                            </div>
                            <div class="timeline-step-label">القائمة المختصرة</div>
                        </div>

                        <!-- Step 3: Interviewed -->
                        <div class="timeline-step {{ $statusIndex >= 2 ? ($statusIndex > 2 ? 'completed' : 'active') : '' }}">
                            <div class="timeline-step-circle">
                                @if($statusIndex > 2) <i class="fas fa-check"></i> @else 3 @endif
                            </div>
                            <div class="timeline-step-label">مقابلة العمل</div>
                        </div>

                        <!-- Step 4: Final Outcome -->
                        <div class="timeline-step {{ $statusIndex >= 3 ? ($isRejected ? 'rejected-step' : 'completed') : '' }}">
                            <div class="timeline-step-circle">
                                @if($statusIndex == 3 && !$isRejected)
                                    <i class="fas fa-trophy"></i>
                                @elseif($isRejected)
                                    <i class="fas fa-times"></i>
                                @else
                                    4
                                @endif
                            </div>
                            <div class="timeline-step-label">{{ $isRejected ? 'لم يتم القبول' : 'مقبول / توظيف' }}</div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Job Summary Details -->
                    <h5 class="fw-bold mb-3 border-bottom pb-2 text-primary">تفاصيل الفرصة الوظيفية</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block">مقر العمل والموقع</small>
                                <span class="fw-bold text-dark">{{ $application->job->location }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block">نوع الدوام</small>
                                <span class="fw-bold text-dark">{{ $application->job->job_type }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Cover Letter -->
                    @if($application->cover_letter)
                        <h5 class="fw-bold mb-3 border-bottom pb-2 text-primary">الرسالة التعريفية المقدمة</h5>
                        <div class="p-3 bg-light rounded-3 mb-4 text-justify" style="white-space: pre-line;">
                            {{ $application->cover_letter }}
                        </div>
                    @endif

                    <!-- CV details -->
                    <h5 class="fw-bold mb-3 border-bottom pb-2 text-primary">المستندات المرسلة</h5>
                    <div class="d-flex align-items-center justify-content-between p-3 border border-dashed rounded-3 bg-light">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                                <i class="far fa-file-pdf fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">نسخة السيرة الذاتية المرفقة</h6>
                                <p class="small text-muted mb-0">تم إرسالها إلى جهة التوظيف بنجاح</p>
                            </div>
                        </div>
                        @if($application->cv_path)
                            <span class="badge bg-success px-3 py-2 rounded-pill"><i class="fas fa-check me-1"></i> مرفقة بالطلب</span>
                        @else
                            <span class="badge bg-warning px-3 py-2 rounded-pill"><i class="fas fa-info-circle me-1"></i> لا يوجد</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Contact / Interview info -->
        <div class="col-lg-4">
            <!-- Interview Information Card (Only visible if status is interviewed/scheduled) -->
            @if($application->status === 'interviewed' || $application->interview_date)
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4 border-start border-primary border-4">
                    <div class="card-header bg-primary text-white p-3">
                        <h5 class="fw-bold mb-0"><i class="far fa-calendar-check me-2"></i>تفاصيل موعد المقابلة</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4 text-center">
                            <div class="display-6 text-primary mb-2"><i class="far fa-clock"></i></div>
                            <h5 class="fw-bold text-dark mb-1">مقابلة عمل مجدولة</h5>
                            <p class="small text-muted">الرجاء الالتزام بالموعد المحدد أدناه</p>
                        </div>
                        <div class="p-3 bg-light rounded-3 mb-3">
                            <small class="text-muted d-block mb-1">التاريخ والوقت:</small>
                            <span class="fw-bold text-primary fs-6">
                                {{ $application->interview_date ? $application->interview_date->format('Y-m-d h:i A') : 'لم يحدد بدقة بعد' }}
                            </span>
                        </div>
                        @if($application->interview_notes)
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">تفاصيل وموقع المقابلة:</small>
                                <span class="fw-semibold text-dark" style="white-space: pre-line;">{{ $application->interview_notes }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Company Overview / Support Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4 text-center">
                <div class="card-body p-4">
                    <i class="fas fa-building fa-3x text-secondary mb-3"></i>
                    <h5 class="fw-bold mb-1">{{ $application->job->company->company_name ?? 'جهة التوظيف' }}</h5>
                    <p class="small text-muted mb-3">جهة توظيف مسجلة ومعتمدة لدى شؤون خريجي جامعة إقليم سبأ</p>
                    @if($application->job->company->website)
                        <a href="{{ $application->job->company->website }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-4">
                            <i class="fas fa-globe me-1"></i> زيارة موقع الشركة
                        </a>
                    @endif
                </div>
            </div>

            <!-- Help/Tips for graduates -->
            <div class="card border-0 shadow-lg rounded-4 bg-light p-4">
                <h6 class="fw-bold text-dark mb-2"><i class="far fa-lightbulb me-1 text-warning"></i> نصائح تتبع التوظيف:</h6>
                <ul class="small text-muted ps-3 mb-0">
                    <li class="mb-2">تحديثات حالة طلبك يتم تفعيلها من قبل ممثلي الجهة الموظفة مباشرة.</li>
                    <li class="mb-2">في حالة قبولك المبدئي وجدولة مقابلة، ستصلك رسالة إشعار فورية هنا وفي صفحة الإشعارات.</li>
                    <li>لأي استفسارات بخصوص فرص العمل، يمكنك استخدام خيار التواصل مع إدارة شؤون الخريجين.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
