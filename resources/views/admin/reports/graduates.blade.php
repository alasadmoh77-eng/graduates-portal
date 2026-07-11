@extends('layouts.app')

@section('title', 'تقرير الخريجين')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-primary">تقرير الخريجين المسجلين</h2>
            <a href="{{ route('admin.reports.graduates.export', request()->all()) }}" class="btn btn-success rounded-pill px-4">
                <i class="fas fa-file-excel me-1"></i> تصدير القائمة (CSV)
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form action="{{ route('admin.reports.graduates') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">بحث بالاسم</label>
                        <input type="text" name="search" class="form-control border-0 bg-light" placeholder="اسم الخريج..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">الكلية</label>
                        <select name="faculty_id" class="form-select border-0 bg-light">
                            <option value="">كل الكليات</option>
                            @foreach(\App\Models\Faculty::orderBy('name_ar')->get() as $faculty)
                                <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>{{ $faculty->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">التخصص</label>
                        <select name="major_id" class="form-select border-0 bg-light">
                            <option value="">كل التخصصات</option>
                            @foreach($majors as $major)
                                <option value="{{ $major->id }}" {{ request('major_id') == $major->id ? 'selected' : '' }}>{{ $major->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">سنة التخرج</label>
                        <input type="number" name="year" class="form-control border-0 bg-light" placeholder="مثلاً: 2023" value="{{ request('year') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill">بحث</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-center">
                            <th class="py-3">الرقم الجامعي</th>
                            <th>الاسم الكامل</th>
                            <th>الكلية</th>
                            <th>التخصص</th>
                            <th>سنة التخرج</th>
                            <th>تاريخ التسجيل</th>
                            <th class="py-3">السجل والشهادات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($graduates as $grad)
                            <tr class="text-center">
                                <td class="fw-bold">{{ $grad->graduate->university_id ?? '---' }}</td>
                                <td>{{ $grad->name }}</td>
                                <td class="small text-muted">{{ $grad->graduate->major->faculty->name_ar ?? '---' }}</td>
                                <td>{{ $grad->graduate->major->name_ar ?? '---' }}</td>
                                <td>{{ $grad->graduate->graduation_year ?? '---' }}</td>
                                <td>{{ $grad->created_at->format('Y-m-d') }}</td>
                                <td>
                                    @can('edit-academic-record')
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('admin.graduates.academic-record.edit', $grad) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2" title="تعديل السجل الأكاديمي">
                                            <i class="fas fa-graduation-cap me-1"></i> السجل
                                        </a>
                                        <a href="{{ route('admin.graduates.grades-certificate.edit', $grad) }}" class="btn btn-sm btn-outline-success rounded-pill px-2" title="تعديل شهادة الدرجات">
                                            <i class="fas fa-file-alt me-1"></i> الشهادة
                                        </a>
                                    </div>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-5 text-center text-muted italic">لا يوجد خريجين يطابقون معايير البحث.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($graduates->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $graduates->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
