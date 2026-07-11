@extends('layouts.app')

@section('title', __('app.payment_review'))

@section('content')
<div class="container-fluid py-4 px-lg-5">
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-primary mb-1"><i class="fas fa-credit-card me-2"></i> {{ __('app.payment_review') }}</h2>
            <p class="text-muted mb-0">مراجعة إثباتات الدفع المرفوعة من الخريجين.</p>
        </div>
        <div class="bg-white px-4 py-2 rounded-pill shadow-sm border text-primary fw-bold">
            {{ __('app.total') }}: <span class="badge bg-primary rounded-pill ms-1">{{ $payments->total() }}</span>
        </div>
    </div>

    <div class="filter-card mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.payments.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">{{ __('app.search') }}</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="{{ __('app.tracking_code') }}...">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">{{ __('app.payment_status') }}</label>
                    <select name="payment_status" class="form-select">
                        <option value="">{{ __('app.all') }}</option>
                        <option value="pending_review" {{ request('payment_status') == 'pending_review' ? 'selected' : '' }}>{{ __('app.payment_pending_review') }}</option>
                        <option value="approved" {{ request('payment_status') == 'approved' ? 'selected' : '' }}>{{ __('app.payment_approved') }}</option>
                        <option value="rejected" {{ request('payment_status') == 'rejected' ? 'selected' : '' }}>{{ __('app.payment_rejected') }}</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill"><i class="fas fa-search me-1"></i> {{ __('app.search') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table custom-table table-hover align-middle mb-0 text-center">
                <thead>
                    <tr>
                        <th>{{ __('app.tracking_code') }}</th>
                        <th>{{ __('app.graduate_name') }}</th>
                        <th>{{ __('app.documents_document_type') }}</th>
                        <th>{{ __('app.fee_amount') }}</th>
                        <th>{{ __('app.payment_status') }}</th>
                        <th>{{ __('app.date') }}</th>
                        <th>{{ __('app.reviewed_by') }}</th>
                        <th>{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td><span class="font-monospace fw-bold text-primary bg-light px-2 py-1 rounded">{{ $payment->tracking_code }}</span></td>
                            <td class="text-start">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($payment->user->name) }}&background=1a237e&color=fff&size=32" class="rounded-circle" width="32" alt="">
                                    <div>
                                        <div class="fw-bold small">{{ $payment->user->name }}</div>
                                        <div class="text-muted" style="font-size:0.7rem;">{{ $payment->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ app()->getLocale() === 'ar' ? $payment->documentType->name_ar : $payment->documentType->name_en }}</td>
                            <td class="fw-bold">{{ number_format($payment->fee_amount, 0) }} {{ $payment->currency }}</td>
                            <td>
                                @php
                                    $badgeMap = [
                                        'pending_review' => 'bg-warning text-dark',
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        'not_required' => 'bg-secondary',
                                    ];
                                    $labelMap = [
                                        'pending_review' => __('app.payment_pending_review'),
                                        'approved' => __('app.payment_approved'),
                                        'rejected' => __('app.payment_rejected'),
                                        'not_required' => __('app.payment_not_required'),
                                    ];
                                @endphp
                                <span class="badge {{ $badgeMap[$payment->payment_status] ?? 'bg-secondary' }} rounded-pill">
                                    {{ $labelMap[$payment->payment_status] ?? $payment->payment_status }}
                                </span>
                            </td>
                            <td><small class="text-muted">{{ $payment->created_at->format('Y-m-d') }}</small></td>
                            <td>
                                @if($payment->paymentReviewedBy)
                                    <small>{{ $payment->paymentReviewedBy->name }}</small>
                                @else
                                    <small class="text-muted">--</small>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    @if($payment->payment_proof_path)
                                        <a href="{{ route('admin.payments.proof', $payment) }}" class="btn btn-sm btn-outline-info rounded-pill" title="{{ __('app.view_proof') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    @if($payment->payment_status === 'pending_review')
                                        <button type="button" class="btn btn-sm btn-success rounded-pill" data-bs-toggle="modal" data-bs-target="#approveModal-{{ $payment->id }}">
                                            <i class="fas fa-check"></i> {{ __('app.approve_payment') }}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger rounded-pill" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $payment->id }}">
                                            <i class="fas fa-times"></i> {{ __('app.reject_payment') }}
                                        </button>
                                    @endif
                                </div>

                                @if($payment->payment_status === 'rejected' && $payment->payment_rejection_reason)
                                    <div class="small text-danger mt-1">
                                        <i class="fas fa-info-circle"></i> {{ $payment->payment_rejection_reason }}
                                    </div>
                                @endif
                            </td>
                        </tr>

                        @if($payment->payment_status === 'pending_review')
                        <div class="modal fade" id="approveModal-{{ $payment->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content rounded-4">
                                    <div class="modal-header border-0">
                                        <h5 class="fw-bold">{{ __('app.approve_payment') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>{{ __('app.confirm_approve_payment', ['code' => $payment->tracking_code]) }}</p>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <form action="{{ route('admin.payments.approve', $payment) }}" method="POST">
                                            @csrf
                                            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                                            <button type="submit" class="btn btn-success rounded-pill px-4">{{ __('app.approve_payment') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="rejectModal-{{ $payment->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content rounded-4">
                                    <div class="modal-header border-0">
                                        <h5 class="fw-bold">{{ __('app.reject_payment') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.payments.reject', $payment) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">{{ __('app.rejection_reason') }} <span class="text-danger">*</span></label>
                                                <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="{{ __('app.rejection_reason_placeholder') }}"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                                            <button type="submit" class="btn btn-danger rounded-pill px-4">{{ __('app.reject_payment') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="py-5 text-center text-muted">
                                <i class="fas fa-credit-card fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">{{ __('app.no_payments_found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($payments->hasPages())
        <div class="mt-4">
            {{ $payments->links() }}
        </div>
    @endif
</div>
@endsection
