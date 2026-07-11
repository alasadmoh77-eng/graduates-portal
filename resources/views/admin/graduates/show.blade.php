@extends('layouts.app')

@section('title', 'تفاصيل الخريج')

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
</style>
@endsection

@section('content')
<div class="container py-4">
    
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none fw-bold"><i class="fas fa-home me-1"></i> لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.graduates') }}" class="text-decoration-none fw-bold">الخريجين</a></li>
                    <li class="breadcrumb-item active" aria-current="page">تفاصيل الخريج</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-primary mb-0">بيانات الخريج: {{ $graduate->name }}</h3>
        </div>
        <a href="{{ route('admin.reports.graduates') }}" class="btn btn-light shadow-sm text-secondary rounded-pill px-4 fw-bold">
            العودة للقائمة <i class="fas fa-arrow-left ms-1"></i>
        </a>
    </div>

    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-lg-5">
            <div class="detail-card p-4 text-center">
                <div class="position-relative d-inline-block mb-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($graduate->name) }}&background=1a237e&color=fff&size=120" class="rounded-circle border border-4 border-white shadow" alt="Profile">
                </div>
                <h4 class="fw-bold mb-1">{{ $graduate->name }}</h4>
                <p class="text-muted small mb-4">{{ $graduate->graduate->major->name_ar ?? '---' }}</p>
                
                <hr class="my-4">
                
                <div class="d-flex flex-column gap-3 text-start" dir="rtl">
                    <div>
                        <div class="info-label">البريد الإلكتروني</div>
                        <div class="info-value text-dark"><i class="fas fa-envelope text-secondary me-2"></i> {{ $graduate->email }}</div>
                    </div>
                    <div>
                        <div class="info-label">الرقم الجامعي</div>
                        <div class="info-value text-dark"><i class="fas fa-id-card text-secondary me-2"></i> {{ $graduate->graduate->university_id ?? '---' }}</div>
                    </div>
                    <div>
                        <div class="info-label">رقم الهاتف</div>
                        <div class="info-value text-dark"><i class="fas fa-phone text-secondary me-2"></i> {{ $graduate->graduate->phone ?? '---' }}</div>
                    </div>
                    <div>
                        <div class="info-label">الكلية</div>
                        <div class="info-value text-dark"><i class="fas fa-university text-secondary me-2"></i> {{ $graduate->graduate->major->faculty->name_ar ?? '---' }}</div>
                    </div>
                    <div>
                        <div class="info-label">التخصص</div>
                        <div class="info-value text-dark"><i class="fas fa-graduation-cap text-secondary me-2"></i> {{ $graduate->graduate->major->name_ar ?? '---' }}</div>
                    </div>
                    <div>
                        <div class="info-label">الدرجة العلمية</div>
                        <div class="info-value text-dark"><i class="fas fa-award text-secondary me-2"></i> {{ $graduate->graduate->major->degree_name_ar ?? '---' }}</div>
                    </div>
                    <div>
                        <div class="info-label">سنة التخرج</div>
                        <div class="info-value text-dark"><i class="fas fa-calendar-alt text-secondary me-2"></i> {{ $graduate->graduate->graduation_year ?? '---' }}</div>
                    </div>
                    <div>
                        <div class="info-label">تاريخ التسجيل بالبوابة</div>
                        <div class="info-value text-dark"><i class="fas fa-clock text-secondary me-2"></i> {{ $graduate->created_at ? $graduate->created_at->format('Y-m-d H:i') : '---' }}</div>
                    </div>
                </div>
                
                @can('edit-academic-record')
                <div class="mt-4 pt-3 border-top d-flex flex-column gap-2 align-items-center w-100">
                    <a href="{{ route('admin.graduates.academic-record.edit', $graduate) }}" class="btn btn-primary rounded-pill px-4 w-75">
                        <i class="fas fa-graduation-cap me-2"></i> إدارة السجل الأكاديمي
                    </a>
                    <a href="{{ route('admin.graduates.grades-certificate.edit', $graduate) }}" class="btn btn-success rounded-pill px-4 w-75 mt-1">
                        <i class="fas fa-file-alt me-2"></i> إدارة شهادة الدرجات
                    </a>
                </div>
                @endcan
            </div>
        </div>

        <!-- Requests Log Card -->
        <div class="col-lg-7">
            <div class="detail-card p-4 h-100">
                <h5 class="fw-bold border-bottom pb-3 mb-4 text-primary"><i class="fas fa-file-invoice me-2"></i> طلبات الوثائق المقدمة</h5>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>كود التتبع</th>
                                <th>نوع المستند</th>
                                <th>اللغة</th>
                                <th>الحالة</th>
                                <th>العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($graduate->documentRequests as $req)
                                <tr>
                                    <td><span class="font-monospace fw-bold text-primary bg-light px-2 py-1 rounded">{{ $req->tracking_code }}</span></td>
                                    <td>{{ $req->documentType->name_ar }}</td>
                                    <td>{{ $req->language }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ __('app.document_status.'.$req->status) ?? $req->status }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.requests.show', $req) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            معالجة <i class="fas fa-external-link-alt ms-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-5 text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 opacity-25"></i>
                                        <p class="mb-0 small">لم يقم الخريج بتقديم أي طلبات استخراج وثائق بعد.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
