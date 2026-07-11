@extends('layouts.app')
@section('title', 'تفاصيل طلب التوظيف')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.employment.applications.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill mb-3">
        <i class="fas fa-arrow-right me-1"></i> رجوع
    </a>
    <h1 class="h3 fw-bold mb-1">تفاصيل طلب التوظيف</h1>
    <span class="badge bg-{{ $application->statusBadge() }} rounded-pill px-3 fs-6">{{ $application->statusLabel() }}</span>
</div>

<div class="row g-4">
    {{-- Info --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h6 class="fw-bold mb-3">المتقدم</h6>
            <p class="mb-1 fw-semibold">{{ $application->graduate->name }}</p>
            <p class="small text-muted mb-1">{{ $application->graduate->email }}</p>
            @if($application->graduate->graduate)
                <p class="small text-muted mb-0">{{ $application->graduate->graduate->major->name_ar ?? '' }} — {{ $application->graduate->graduate->graduation_year ?? '' }}</p>
            @endif
        </div>
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h6 class="fw-bold mb-3">الوظيفة</h6>
            <p class="fw-semibold mb-1">{{ $application->job->title }}</p>
            <p class="small text-muted mb-1">{{ $application->job->company->company_name ?? '—' }}</p>
            <p class="small text-muted mb-0">{{ $application->job->location ?? '' }} · {{ $application->job->job_type ?? '' }}</p>
            @if($application->interview_date)
                <div class="alert alert-info mt-3 small">
                    <strong>موعد المقابلة:</strong> {{ $application->interview_date->format('Y/m/d H:i') }}<br>
                    @if($application->interview_notes) <span>{{ $application->interview_notes }}</span> @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Actions + Cover Letter --}}
    <div class="col-lg-8">
        @if($application->cover_letter)
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h6 class="fw-bold mb-3">خطاب التقديم</h6>
                <p class="text-muted">{{ $application->cover_letter }}</p>
            </div>
        @endif

        @if($application->employer_notes)
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h6 class="fw-bold mb-3">ملاحظات صاحب العمل</h6>
                <p class="text-muted">{{ $application->employer_notes }}</p>
            </div>
        @endif

        {{-- Pipeline Status Update --}}
        @if(in_array($application->status, ['new', 'shortlisted', 'interviewed']))
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h6 class="fw-bold mb-3">تحديث الحالة</h6>
                <form method="POST" action="{{ route('admin.employment.applications.status', $application) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">الحالة الجديدة</label>
                        <select name="status" class="form-select rounded-3" required>
                            @foreach($application->nextStatuses() as $ns)
                                <option value="{{ $ns }}">{{ $application->statusLabel() }} → {{ (new \App\Models\JobApplication(['status' => $ns]))->statusLabel() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات (اختياري)</label>
                        <textarea name="employer_notes" class="form-control rounded-3" rows="2" placeholder="إضافة ملاحظة...">{{ $application->employer_notes }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">تحديث الحالة</button>
                </form>
            </div>
        @endif

        {{-- Schedule Interview --}}
        @if($application->status === 'shortlisted')
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold mb-3">جدولة مقابلة</h6>
                <form method="POST" action="{{ route('admin.employment.applications.interview', $application) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">تاريخ ووقت المقابلة</label>
                            <input type="datetime-local" name="interview_date" class="form-control rounded-3 date-input" required dir="ltr" lang="en">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ملاحظات المقابلة</label>
                            <input type="text" name="interview_notes" class="form-control rounded-3" placeholder="مكان أو رابط المقابلة...">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-4 mt-3">جدولة المقابلة</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
