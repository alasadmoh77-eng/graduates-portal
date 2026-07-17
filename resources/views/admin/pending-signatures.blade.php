@extends('layouts.app')

@section('title', 'التوقيعات المعلقة')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <i class="fas fa-file-signature text-primary me-2"></i>التوقيعات المعلقة
        </h2>
        <div>
            <a href="{{ route('admin.ready-signatures') }}" class="btn btn-outline-success rounded-pill me-2">
                <i class="fas fa-check-circle me-1"></i> التوقيعات الجاهزة
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="fas fa-arrow-right me-1"></i> لوحة التحكم
            </a>
        </div>
    </div>

    @if(Auth::user()->signer_role)
        <div class="alert alert-primary rounded-3 mb-3">
            <i class="fas fa-id-badge me-1"></i> دورك التوقيعي: <strong>{{ Auth::user()->signer_role }}</strong>
        </div>
    @endif

    @if($pendingDocs->isEmpty() && !request()->hasAny(['search', 'document_type_id', 'date_from', 'date_to']))
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h5 class="fw-bold text-dark">لا توجد توقيعات معلقة</h5>
                <p class="text-muted">جميع الوثائق التي تتطلب دورك التوقيعي قد تم توقيعها.</p>
            </div>
        </div>
    @else
        {{-- Filters --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-3">
                <h6 class="fw-bold mb-0 text-secondary"><i class="fas fa-filter me-2"></i> أدوات التصفية والبحث</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.pending-signatures') }}" method="GET" class="row g-3">
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <label class="form-label small fw-bold text-muted">البحث (الاسم، رقم الطلب)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" value="{{ request('search') }}" placeholder="ابحث...">
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6">
                        <label class="form-label small fw-bold text-muted">نوع الوثيقة</label>
                        <select name="document_type_id" class="form-select">
                            <option value="">كل الأنواع</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ request('document_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-4">
                        <label class="form-label small fw-bold text-muted">من تاريخ</label>
                        <input type="text" name="date_from" class="form-control date-picker-input" value="{{ request('date_from') }}" placeholder="YYYY-MM-DD" dir="ltr" lang="en" readonly>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-4">
                        <label class="form-label small fw-bold text-muted">إلى تاريخ</label>
                        <input type="text" name="date_to" class="form-control date-picker-input" value="{{ request('date_to') }}" placeholder="YYYY-MM-DD" dir="ltr" lang="en" readonly>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold"><i class="fas fa-filter me-1"></i> تصفية</button>
                        <a href="{{ route('admin.pending-signatures') }}" class="btn btn-outline-secondary rounded-pill px-3"><i class="fas fa-times"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white p-4 border-0">
                <h5 class="fw-bold mb-0">الوثائق بانتظار توقيعك ({{ $pendingDocs->total() }})</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>رقم الطلب</th>
                                <th>الخريج</th>
                                <th>نوع الوثيقة</th>
                                <th>تاريخ الإرسال</th>
                                <th>حالة التوقيعات</th>
                                <th class="text-center pe-4">إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingDocs as $doc)
                                @php
                                    $request = $doc->documentRequest;
                                    $required = $doc->getRequiredSigners();
                                    $signedRoles = $doc->signatures->pluck('role_title')->toArray();
                                    $userSigRole = Auth::user()->signer_role;
                                    $currentSigner = $doc->getCurrentSigner();
                                    $canSignThis = $userSigRole && $currentSigner === $userSigRole;
                                    $allRolesSigned = $currentSigner === null;
                                @endphp
                                <tr>
                                    <td class="ps-4">{{ ($pendingDocs->currentPage() - 1) * $pendingDocs->perPage() + $loop->iteration }}</td>
                                    <td><span class="font-monospace small fw-bold">{{ $request->tracking_code ?? '—' }}</span></td>
                                    <td>
                                        <strong>{{ $request->user->name ?? '—' }}</strong>
                                        <br><small class="text-muted">{{ $request->user->graduate->university_id ?? '—' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                            {{ app()->getLocale() == 'ar' ? ($request->documentType->name_ar ?? '—') : ($request->documentType->name_en ?? '—') }}
                                        </span>
                                    </td>
                                    <td style="direction: ltr; text-align: right;">{{ $doc->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($required as $role)
                                                @if(in_array($role, $signedRoles))
                                                    @php $sig = $doc->signatures->firstWhere('role_title', $role); @endphp
                                                    <span class="badge bg-success bg-opacity-10 text-success small" title="وقع: {{ $sig->user->name ?? '' }} | {{ $sig->signed_at->format('Y-m-d H:i') }}">
                                                        <i class="fas fa-check-circle me-1"></i>{{ $role }}
                                                    </span>
                                                @elseif($currentSigner === $role)
                                                    <span class="badge bg-warning bg-opacity-10 text-warning small fw-bold">
                                                        <i class="fas fa-arrow-circle-right me-1"></i>{{ $role }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary small">
                                                        <i class="far fa-clock me-1"></i>{{ $role }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-center pe-4">
                                        @if($allRolesSigned && !$doc->all_signed_at)
                                            <span class="badge bg-warning rounded-pill">
                                                <i class="fas fa-check-circle me-1"></i> بانتظار الاعتماد
                                            </span>
                                        @elseif($canSignThis)
                                            <form method="POST" action="{{ route('admin.documents.sign', $doc) }}" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3" onclick="return confirm('هل أنت متأكد من توقيع هذه الوثيقة بصفتك: {{ $userSigRole }}؟')">
                                                    <i class="fas fa-pen-fancy me-1"></i> توقيع
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-success rounded-pill">مكتملة</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $pendingDocs->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
