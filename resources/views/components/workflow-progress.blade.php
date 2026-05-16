{{--
    x-workflow-progress — Request status progression bar
    Shows the full workflow: SUBMITTED → UNDER_REVIEW → APPROVED → READY → ISSUED
    A REJECTED status replaces the current step with a red rejected indicator.

    Props:
      $currentStatus : string — the request's current status
--}}
@props(['currentStatus'])

@php
$steps = [
    'SUBMITTED'    => ['icon' => 'fa-paper-plane',    'label' => __('app.document_status.SUBMITTED')],
    'UNDER_REVIEW' => ['icon' => 'fa-hourglass-half', 'label' => __('app.document_status.UNDER_REVIEW')],
    'APPROVED'     => ['icon' => 'fa-check',          'label' => __('app.document_status.APPROVED')],
    'READY'        => ['icon' => 'fa-box',            'label' => __('app.document_status.READY')],
    'ISSUED'       => ['icon' => 'fa-file-pdf',       'label' => __('app.document_status.ISSUED')],
];

$order   = array_keys($steps);
$current = strtoupper($currentStatus);
$isRejected = $current === 'REJECTED';

// If rejected, highlight up through SUBMITTED as completed, mark UNDER_REVIEW as rejected
$rejectedAt = 'UNDER_REVIEW';

$currentIndex = $isRejected
    ? array_search($rejectedAt, $order)
    : array_search($current, $order);
@endphp

<div class="ds-workflow">
    @foreach($steps as $key => $step)
        @php
            $index = array_search($key, $order);

            if ($isRejected) {
                if ($index < $currentIndex)        $state = 'completed';
                elseif ($index === $currentIndex)  $state = 'rejected';
                else                               $state = 'pending';
            } else {
                if ($index < $currentIndex)        $state = 'completed';
                elseif ($index === $currentIndex)  $state = 'active';
                else                               $state = 'pending';
            }

            $icon = ($state === 'completed') ? 'fa-check' : (($state === 'rejected') ? 'fa-times' : $step['icon']);
        @endphp

        <div class="ds-workflow-step {{ $state }}">
            <div class="ds-workflow-icon">
                <i class="fas {{ $icon }}"></i>
            </div>
            <div class="ds-workflow-label">{{ $step['label'] }}</div>
        </div>
    @endforeach
</div>

@if($isRejected)
    <div class="text-center mt-2 mb-1">
        <x-status-badge :status="'REJECTED'" />
    </div>
@endif
