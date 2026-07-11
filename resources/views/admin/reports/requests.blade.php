@extends('layouts.app')

@section('title', 'تقرير طلبات المستندات')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-primary">تقرير طلبات المستندات</h2>
            <a href="{{ route('admin.reports.requests.export', request()->all()) }}" class="btn btn-success rounded-pill px-4">
                <i class="fas fa-file-excel me-1"></i> تصدير التقرير (CSV)
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form action="{{ route('admin.reports.requests') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">الحالة</label>
                        <select name="status" class="form-select border-0 bg-light">
                            <option value="">كل الحالات</option>
                            @foreach(['SUBMITTED', 'UNDER_REVIEW', 'APPROVED', 'PENDING_SIGNATURES', 'REJECTED', 'READY', 'ISSUED'] as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">نوع المستند</label>
                        <select name="type_id" class="form-select border-0 bg-light">
                            <option value="">كل الأنواع</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>{{ $type->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">من تاريخ</label>
                        <input type="text" name="date_from" class="form-control border-0 bg-light date-picker-input" value="{{ request('date_from') }}" placeholder="YYYY-MM-DD" dir="ltr" lang="en" readonly autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">إلى تاريخ</label>
                        <input type="text" name="date_to" class="form-control border-0 bg-light date-picker-input" value="{{ request('date_to') }}" placeholder="YYYY-MM-DD" dir="ltr" lang="en" readonly autocomplete="off">
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
                            <th class="py-3">كود التتبع</th>
                            <th>الخريج</th>
                            <th>نوع المستند</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            <tr class="text-center">
                                <td class="fw-bold">{{ $req->tracking_code }}</td>
                                <td>{{ $req->user->name }}</td>
                                <td>{{ $req->documentType->name_ar }}</td>
                                <td>
                                    <span class="badge rounded-pill bg-{{ $req->status == 'ISSUED' ? 'success' : ($req->status == 'REJECTED' ? 'danger' : 'primary') }}">
                                        {{ $req->status }}
                                    </span>
                                </td>
                                <td>{{ $req->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-5 text-center text-muted italic">لا توجد بيانات تطابق البحث حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($requests->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
