@extends('layouts.app')

@section('title', __('app.graduate_registry'))

@section('content')
<div class="container-fluid py-4 px-lg-5 animate__animated animate__fadeIn">

    <!-- Header & Action Buttons -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-primary mb-1">
                <i class="fas fa-user-graduate me-2"></i> {{ __('app.graduate_registry') }}
            </h2>
            <p class="text-muted mb-0">
                {{ app()->getLocale() == 'ar' ? 'إدارة ومزامنة قوائم الخريجين الرسمية المعتمدة من الجامعة.' : 'Manage and synchronize official university approved graduates.' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            @if(app()->environment(['local', 'testing']))
                <form action="{{ route('admin.graduate-registry.clear-test-data') }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد من تفريغ قائمة الخريجين المعتمدين التجريبية؟ هذا الإجراء سيحذف سجلات approved_graduates فقط، ولن يحذف حسابات الخريجين أو وثائقهم أو سجلاتهم الأكاديمية.' : 'Are you sure you want to empty the test approved graduates list? This action will delete only approved_graduates records, and will not delete graduate accounts, documents, or academic records.' }}')" class="d-inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fas fa-trash-alt me-1"></i> {{ app()->getLocale() == 'ar' ? 'تفريغ قائمة الخريجين المعتمدين ' : 'Empty Test Approved Graduates List' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- KPI Statistics cards -->
    <div class="row g-4 mb-5">
        <!-- Approved Graduates Count -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative h-100" style="transition: all 0.3s;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-4 m-0" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user-check fa-lg"></i>
                        </div>
                    </div>
                    <h2 class="fw-extrabold mb-1 text-dark font-monospace" style="font-size: 2.2rem;">{{ number_format($totalCount) }}</h2>
                    <p class="mb-0 text-secondary small fw-bold">{{ __('app.approved_graduates_count') }}</p>
                </div>
                <div style="height: 4px; background: var(--primary-blue);"></div>
            </div>
        </div>

        <!-- Last Import Date -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative h-100" style="transition: all 0.3s;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="icon-box bg-success bg-opacity-10 text-success rounded-4 m-0" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-history fa-lg"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-1 text-dark" style="font-size: 1.5rem; margin-top: 10px;">
                        {{ $lastImport ? ($lastImport instanceof \Carbon\Carbon ? $lastImport->format('Y-m-d H:i') : $lastImport) : '—' }}
                    </h4>
                    <p class="mb-0 text-secondary small fw-bold">{{ __('app.last_import') }}</p>
                </div>
                <div style="height: 4px; background: var(--success-green, #10b981);"></div>
            </div>
        </div>
    </div>

    <!-- Import Section & Filters -->
    <div class="row g-4 mb-4">
        <!-- Excel Upload Card -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-file-excel text-success me-2"></i> {{ __('app.excel_file') }}
                    </h5>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <form action="{{ route('admin.graduate-registry.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="upload-dropzone text-center p-5 mb-4 border border-dashed rounded-4 bg-light position-relative" style="border-width: 2px; border-color: #cbd5e1; cursor: pointer; transition: background-color 0.2s;">
                            <i class="fas fa-cloud-upload-alt fa-3x text-secondary mb-3"></i>
                            <h6 class="fw-bold text-dark mb-1">{{ __('app.excel_file') }}</h6>
                            <p class="text-muted small mb-3">xlsx, xls (Max 10MB)</p>
                            
                            <input type="file" name="excel_file" class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;" required id="excelFileInput">
                            <div id="file-name-display" class="fw-bold text-primary small mt-2"></div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-file-import"></i> {{ __('app.import') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Search & Filter Card -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-filter text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'بحث وتصفية' : 'Search & Filters' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.graduate-registry.index') }}" method="GET" class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">{{ app()->getLocale() == 'ar' ? 'البحث العام (الاسم أو الرقم الأكاديمي)' : 'General Search (Name or ID)' }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0" value="{{ request('search') }}" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل اسم الطالب أو الرقم الجامعي...' : 'Enter student name or ID...' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">{{ __('app.major') }}</label>
                            <input type="text" name="major" class="form-control" value="{{ request('major') }}" placeholder="{{ app()->getLocale() == 'ar' ? 'مثال: هندسة البرمجيات' : 'e.g. Software Engineering' }}">
                        </div>
                        <div class="col-12 mt-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold flex-grow-1 shadow-sm">
                                    <i class="fas fa-search me-1"></i> {{ app()->getLocale() == 'ar' ? 'بحث' : 'Search' }}
                                </button>
                                <a href="{{ route('admin.graduate-registry.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                                    {{ app()->getLocale() == 'ar' ? 'إعادة تعيين' : 'Reset' }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-header bg-white border-bottom p-4">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fas fa-list text-secondary me-2"></i> {{ app()->getLocale() == 'ar' ? 'قائمة الخريجين المعتمدين' : 'Approved Graduates List' }}
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center" style="border-color: #f1f5f9;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-start">{{ __('app.university_id') }}</th>
                            <th class="text-start">{{ __('app.name') }}</th>
                            <th>{{ __('app.email') }}</th>
                            <th>{{ __('app.faculty') }}</th>
                            <th>{{ __('app.major') }}</th>
                            <th>{{ __('app.graduation_year') }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'تاريخ المزامنة' : 'Synced At' }}</th>
                            <th class="pe-4">{{ app()->getLocale() == 'ar' ? 'العمليات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($graduates as $graduate)
                            <tr>
                                <td class="ps-4 text-start">
                                    <span class="font-monospace fw-bold text-primary bg-light px-2 py-1 rounded">{{ $graduate->university_id }}</span>
                                </td>
                                <td class="text-start">
                                    <div class="fw-bold text-dark">{{ $graduate->name }}</div>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $graduate->email ?? '—' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 px-3 py-2 rounded-pill small">
                                        {{ $graduate->college ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20 px-3 py-2 rounded-pill small">
                                        {{ $graduate->major }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $graduate->graduation_year }}</span>
                                </td>
                                <td class="text-muted small">
                                    {{ $graduate->updated_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="pe-4">
                                    @php
                                        $associatedGraduate = $graduate->graduate;
                                        $associatedUser = $associatedGraduate ? $associatedGraduate->user : null;
                                    @endphp

                                    @if($associatedUser)
                                        @if($associatedUser->is_active)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-2 rounded-pill small mb-1 d-inline-block">
                                                {{ app()->getLocale() == 'ar' ? 'الحساب نشط' : 'Account Active' }}
                                            </span>
                                            @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                                                <form action="{{ route('admin.graduate-registry.freeze-account', $graduate->id) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد من تجميد حساب هذا الخريج؟ لن يتم حذف أي بيانات، ولكن لن يستطيع الخريج تسجيل الدخول حتى يتم إلغاء التجميد.' : 'Are you sure you want to freeze this graduate account? No data will be deleted, but the graduate will not be able to log in until it is unfrozen.' }}')" class="d-inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-warning btn-sm rounded-pill px-3 fw-bold ms-1">
                                                        <i class="fas fa-user-slash me-1"></i> {{ app()->getLocale() == 'ar' ? 'تجميد الحساب' : 'Freeze Account' }}
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-3 py-2 rounded-pill small mb-1 d-inline-block">
                                                {{ app()->getLocale() == 'ar' ? 'الحساب مجمد' : 'Account Frozen' }}
                                            </span>
                                            @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                                                <form action="{{ route('admin.graduate-registry.unfreeze-account', $graduate->id) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد من إلغاء تجميد حساب هذا الخريج؟ سيتمكن الخريج من تسجيل الدخول مرة أخرى.' : 'Are you sure you want to unfreeze this graduate account? The graduate will be able to log in again.' }}')" class="d-inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold ms-1">
                                                        <i class="fas fa-unlock me-1"></i> {{ app()->getLocale() == 'ar' ? 'إلغاء التجميد' : 'Unfreeze Account' }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20 px-3 py-2 rounded-pill small">
                                            {{ app()->getLocale() == 'ar' ? 'لا يوجد حساب مسجل' : 'No Registered Account' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-5 text-center text-muted">
                                    <i class="fas fa-user-slash fa-3x mb-3 text-light"></i>
                                    <p class="mb-0 small">{{ app()->getLocale() == 'ar' ? 'لا توجد سجلات مطابقة في سجل الخريجين.' : 'No records found in graduate registry.' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($graduates->hasPages())
                <div class="p-4 border-top d-flex justify-content-center">
                    {{ $graduates->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Show selected filename
        $('#excelFileInput').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $('#file-name-display').html('<i class="fas fa-file-alt text-success me-1"></i> ' + fileName);
                $('.upload-dropzone').css('background-color', 'rgba(16, 185, 129, 0.05)');
            } else {
                $('#file-name-display').text('');
                $('.upload-dropzone').css('background-color', '');
            }
        });
    });
</script>
@endsection
