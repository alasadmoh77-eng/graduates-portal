@extends('layouts.app')

@section('title', 'إدارة رسوم الوثائق')

@section('content')
<div class="container-fluid py-4 px-lg-5">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-primary mb-1"><i class="fas fa-money-bill-wave me-2"></i> إدارة رسوم الوثائق</h2>
            <p class="text-muted mb-0">تحديد وتعديل الرسوم المالية المفروضة على طلبات وثائق الخريجين.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm mb-4">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 text-center custom-table table-hover">
                    <thead>
                        <tr class="bg-light">
                            <th class="text-start ps-4">اسم الوثيقة</th>
                            <th>حالة الرسوم</th>
                            <th>قيمة الرسوم (YER)</th>
                            <th>آخر تحديث</th>
                            <th class="text-end pe-4">العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentTypes as $type)
                            <tr>
                                <td class="text-start ps-4">
                                    <div class="fw-bold text-dark">
                                        <i class="fas {{ $type->code === 'ACADEMIC_RECORD' ? 'fa-graduation-cap' : 'fa-award' }} text-secondary me-2"></i>
                                        {{ $type->name_ar }}
                                    </div>
                                    <div class="small text-muted font-monospace">{{ $type->name_en }}</div>
                                </td>
                                <td>
                                    @if($type->payment_required)
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                            <i class="fas fa-money-bill-wave me-1"></i> مدفوعة
                                        </span>
                                    @else
                                        <span class="badge bg-success text-white rounded-pill px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i> مجانية
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <form id="form-{{ $type->id }}" action="{{ route('admin.document-fees.update', $type->id) }}" method="POST" class="d-inline-flex align-items-center gap-2">
                                        @csrf
                                        <select name="payment_required" class="form-select form-select-sm rounded-pill status-select" data-id="{{ $type->id }}" style="width: 110px;">
                                            <option value="1" {{ $type->payment_required ? 'selected' : '' }}>مدفوعة</option>
                                            <option value="0" {{ !$type->payment_required ? 'selected' : '' }}>مجانية</option>
                                        </select>
                                        <div class="input-group input-group-sm" style="width: 150px;">
                                            <input type="number" name="fee_amount" id="amount-{{ $type->id }}" class="form-control rounded-pill-start amount-input" value="{{ (int)$type->fee_amount }}" min="0" required {{ !$type->payment_required ? 'disabled' : '' }}>
                                            <span class="input-group-text bg-light rounded-pill-end">ريال</span>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <div class="small text-muted font-monospace">{{ $type->updated_at ? $type->updated_at->format('Y-m-d H:i') : '--' }}</div>
                                </td>
                                <td class="text-end pe-4">
                                    <button type="submit" form="form-{{ $type->id }}" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">
                                        <i class="fas fa-save me-1"></i> حفظ
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center p-5 text-muted">
                                    لا توجد وثائق متوفرة في النظام.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusSelects = document.querySelectorAll('.status-select');
        statusSelects.forEach(select => {
            select.addEventListener('change', function () {
                const id = this.dataset.id;
                const amountInput = document.getElementById('amount-' + id);
                if (this.value === '1') {
                    amountInput.removeAttribute('disabled');
                    if (amountInput.value === '0' || amountInput.value === '') {
                        amountInput.value = '1000'; // Default default fee if toggling back
                    }
                } else {
                    amountInput.setAttribute('disabled', 'disabled');
                    amountInput.value = '0';
                }
            });
        });
    });
</script>
<style>
    .rounded-pill-start {
        border-top-left-radius: 50rem !important;
        border-bottom-left-radius: 50rem !important;
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    .rounded-pill-end {
        border-top-right-radius: 50rem !important;
        border-bottom-right-radius: 50rem !important;
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        border-left: 0 !important;
    }
    [dir="rtl"] .rounded-pill-start {
        border-top-right-radius: 50rem !important;
        border-bottom-right-radius: 50rem !important;
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
    [dir="rtl"] .rounded-pill-end {
        border-top-left-radius: 50rem !important;
        border-bottom-left-radius: 50rem !important;
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
        border-right: 0 !important;
    }
</style>
@endsection
