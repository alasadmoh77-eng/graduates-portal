{{--
    x-status-badge — Centralized status badge component
    Props:
      $status : string (SUBMITTED|UNDER_REVIEW|APPROVED|READY|ISSUED|REJECTED)
      $size   : 'sm'|'md' (default: 'md')
--}}
@props(['status', 'size' => 'md'])

@php
$statusMap = [
    'SUBMITTED'    => ['key' => 'submitted',    'icon' => 'fa-paper-plane',   'label' => __('app.document_status.SUBMITTED')],
    'UNDER_REVIEW' => ['key' => 'under_review', 'icon' => 'fa-hourglass-half','label' => __('app.document_status.UNDER_REVIEW')],
    'APPROVED'     => ['key' => 'approved',     'icon' => 'fa-check-double',  'label' => __('app.document_status.APPROVED')],
    'PENDING_SIGNATURES' => ['key' => 'pending_signatures', 'icon' => 'fa-file-signature', 'label' => __('app.document_status.PENDING_SIGNATURES')],
    'READY'        => ['key' => 'ready',        'icon' => 'fa-box',           'label' => __('app.document_status.READY')],
    'ISSUED'       => ['key' => 'issued',       'icon' => 'fa-file-pdf',      'label' => __('app.document_status.ISSUED')],
    'REJECTED'     => ['key' => 'rejected',     'icon' => 'fa-times-circle',  'label' => __('app.document_status.REJECTED')],
];

$conf = $statusMap[strtoupper($status)] ?? ['key' => 'default', 'icon' => 'fa-circle', 'label' => $status];
$sizeClass = $size === 'sm' ? 'ds-status-sm' : '';
@endphp

<span class="ds-status-badge ds-status-{{ $conf['key'] }} {{ $sizeClass }}">
    <i class="fas {{ $conf['icon'] }}"></i>
    {{ $conf['label'] }}
</span>
