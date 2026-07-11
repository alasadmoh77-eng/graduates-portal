@extends('layouts.app')

@section('title', 'تفاصيل طلب التوظيف | بوابة الخريجين')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('employer.applications.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-right me-1"></i> العودة إلى الطلبات
        </a>
    </div>

    <div class="row g-4">
        <!-- Candidate Profile & Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-gradient-primary text-white p-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <span class="badge bg-light text-primary mb-2 px-3 py-2 rounded-pill fw-bold">طلب توظيف</span>
                            <h2 class="h3 fw-bold mb-1">{{ $application->graduate->name }}</h2>
                            <p class="mb-0 opacity-75">متقدم لوظيفة: <span class="fw-bold">{{ $application->job->title }}</span></p>
                        </div>
                        <div>
                            <span class="badge bg-{{ $application->statusBadge() }} px-4 py-3 rounded-pill text-white fs-6 shadow-sm">
                                {{ $application->statusLabel() }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2 text-primary">معلومات المتقدم</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block">البريد الإلكتروني</small>
                                <span class="fw-bold">{{ $application->graduate->email }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block">رقم الهاتف</small>
                                <span class="fw-bold">{{ $application->graduate->graduate->phone ?? 'غير متوفر' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block">التخصص الدراسي</small>
                                <span class="fw-bold">{{ $application->graduate->graduate->major->name_ar ?? 'غير متوفر' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block">سنة التخرج</small>
                                <span class="fw-bold">{{ $application->graduate->graduate->graduation_year ?? 'غير متوفر' }}</span>
                            </div>
                        </div>
                    </div>

                    @if($application->cover_letter)
                        <h5 class="fw-bold mb-3 border-bottom pb-2 text-primary">الرسالة التعريفية (Cover Letter)</h5>
                        <div class="p-3 bg-light rounded-3 mb-4 text-justify" style="white-space: pre-line;">
                            {{ $application->cover_letter }}
                        </div>
                    @endif

                    <h5 class="fw-bold mb-3 border-bottom pb-2 text-primary">السيرة الذاتية وثائق التقديم</h5>
                    <div class="d-flex align-items-center justify-content-between p-3 border border-dashed rounded-3 bg-light">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                                <i class="far fa-file-pdf fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">السيرة الذاتية للمتقدم</h6>
                                <p class="small text-muted mb-0">صيغة PDF / Word جاهزة للمراجعة</p>
                            </div>
                        </div>
                        @if($application->cv_path)
                            <a href="{{ route('employer.applications.cv', $application->id) }}" class="btn btn-primary rounded-pill px-4">
                                <i class="fas fa-download me-1"></i> تحميل السيرة الذاتية
                            </a>
                        @else
                            <span class="text-danger small fw-bold">لم يتم إرفاق سيرة ذاتية</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Interview Details (if scheduled) -->
            @if($application->status === 'interviewed' || $application->interview_date)
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4 border-start border-primary border-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-primary mb-3">تفاصيل مقابلة العمل</h5>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3">
                                    <small class="text-muted d-block">تاريخ ووقت المقابلة</small>
                                    <span class="fw-bold text-primary">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ $application->interview_date ? $application->interview_date->format('Y-m-d h:i A') : 'لم يحدد بعد' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3">
                                    <small class="text-muted d-block">ملاحظات وتفاصيل المقابلة</small>
                                    <span class="fw-bold">{{ $application->interview_notes ?? 'لا توجد ملاحظات' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Pipeline Status Controls -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-dark text-white p-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-tasks me-2"></i>إجراءات حالة الطلب</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('employer.applications.status', $application->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">تحديث الحالة إلى:</label>
                            <select class="form-select rounded-3 p-2.5" id="status" name="status" required>
                                <option value="" disabled selected>-- اختر حالة جديدة --</option>
                                <option value="shortlisted" {{ $application->status === 'shortlisted' ? 'selected' : '' }}>إدراج في القائمة المختصرة (Shortlist)</option>
                                <option value="interviewed" {{ $application->status === 'interviewed' ? 'selected' : '' }}>جدولة/دعوة لمقابلة شخصية (Interview)</option>
                                <option value="hired" {{ $application->status === 'hired' ? 'selected' : '' }}>قبول وتوظيف المتقدم (Hire)</option>
                                <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>رفض الطلب (Reject)</option>
                            </select>
                        </div>

                        <!-- Interview Scheduler Fields (hidden by default, shown via JS if status = interviewed) -->
                        <div id="interview-fields" class="d-none border p-3 rounded-3 mb-3 bg-light">
                            <h6 class="fw-bold mb-3 text-primary"><i class="far fa-clock me-1"></i>تفاصيل المقابلة</h6>
                            <div class="mb-3">
                                <label for="interview_date" class="form-label small fw-bold">تاريخ ووقت المقابلة:</label>
                                <input type="datetime-local" class="form-control date-input" id="interview_date" name="interview_date" value="{{ $application->interview_date ? $application->interview_date->format('Y-m-d\TH:i') : '' }}" dir="ltr" lang="en">
                            </div>
                            <div class="mb-0">
                                <label for="interview_notes" class="form-label small fw-bold">موقع المقابلة أو رابط الاجتماع/ملاحظات إضافية:</label>
                                <textarea class="form-control" id="interview_notes" name="interview_notes" rows="3" placeholder="أدخل مكان المقابلة أو رابط زووم/جوجل ميت والمستندات المطلوبة..."></textarea>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="employer_notes" class="form-label fw-bold">ملاحظات صاحب العمل (خاصة بالشركة):</label>
                            <textarea class="form-control rounded-3" id="employer_notes" name="employer_notes" rows="4" placeholder="اكتب أي ملاحظات داخلية حول المتقدم هنا...">{{ $application->employer_notes }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-gradient w-100 rounded-pill py-2.5 text-white fw-bold shadow-sm" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border: none;">
                            <i class="fas fa-save me-1"></i> تحديث وحفظ الحالة
                        </button>
                    </form>
                </div>
            </div>

            <!-- Graduate Card Profile Link Summary -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-4 text-center">
                    <i class="fas fa-user-graduate fa-3x text-secondary mb-3"></i>
                    <h5 class="fw-bold mb-1">{{ $application->graduate->name }}</h5>
                    <p class="small text-muted mb-3">{{ $application->graduate->graduate->major->name_ar ?? 'تخصص خريج' }}</p>
                    <hr>
                    <div class="d-grid">
                        <a href="{{ route('alumni.contact') }}" class="btn btn-outline-primary btn-sm rounded-pill py-2">
                            <i class="far fa-envelope me-1"></i> إرسال رسالة مباشرة للمتقدم
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        function toggleInterviewFields() {
            if ($('#status').val() === 'interviewed') {
                $('#interview-fields').removeClass('d-none');
                $('#interview_date').prop('required', true);
            } else {
                $('#interview-fields').addClass('d-none');
                $('#interview_date').prop('required', false);
            }
        }

        $('#status').change(toggleInterviewFields);
        toggleInterviewFields(); // Trigger initially on page load if status is interviewed
    });
</script>
@endsection
