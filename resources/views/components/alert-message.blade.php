{{--
    x-alert-message — Contextual dismissible alert
    Props:
      $type      : 'info'|'success'|'warning'|'danger'
      $title     : (optional) Bold heading
      $message   : Message text (or use slot)
      $dismissible: (boolean, default true)
--}}
@props([
    'type'        => 'info',
    'title'       => '',
    'message'     => '',
    'dismissible' => true,
])

@php
$iconMap = [
    'info'    => ['icon' => 'fa-info-circle',        'color' => 'text-blue-600'],
    'success' => ['icon' => 'fa-check-circle',       'color' => 'text-green-600'],
    'warning' => ['icon' => 'fa-exclamation-triangle','color' => 'text-yellow-600'],
    'danger'  => ['icon' => 'fa-times-circle',       'color' => 'text-red-600'],
];
$ico = $iconMap[$type] ?? $iconMap['info'];
@endphp

<div class="ds-alert ds-alert-{{ $type }} mb-3" role="alert">
    <i class="fas {{ $ico['icon'] }} ds-alert-icon text-{{ $type === 'danger' ? 'danger' : ($type === 'warning' ? 'warning' : ($type === 'success' ? 'success' : 'primary')) }} fa-lg"></i>
    <div class="flex-grow-1">
        @if($title)
            <div class="fw-bold mb-1 text-dark">{{ $title }}</div>
        @endif
        @if($message)
            <div class="small text-dark">{{ $message }}</div>
        @else
            {{ $slot }}
        @endif
    </div>
    @if($dismissible)
        <button type="button" class="btn-close ms-auto flex-shrink-0" data-bs-dismiss="alert" aria-label="Close" style="font-size:0.75rem;"></button>
    @endif
</div>
