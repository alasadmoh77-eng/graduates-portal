{{--
    x-section-card — Reusable content card with optional header
    Props:
      $title      : (optional) Card header title
      $icon       : (optional) FontAwesome icon class for header
      $headerClass: (optional) Extra classes for header div (e.g., 'bg-primary bg-opacity-10')
      $titleClass : (optional) Extra classes for title (e.g., 'text-primary')
      $noPadding  : (optional) Remove body padding (boolean)
--}}
@props([
    'title'       => '',
    'icon'        => '',
    'headerClass' => '',
    'titleClass'  => 'text-dark',
    'noPadding'   => false,
])

<div class="ds-card">
    @if($title)
        <div class="ds-card-header {{ $headerClass }}">
            <h5 class="fw-bold {{ $titleClass }} mb-0">
                @if($icon)
                    <i class="fas {{ $icon }} me-2"></i>
                @endif
                {{ $title }}
            </h5>
            @isset($action)
                <div>{{ $action }}</div>
            @endisset
        </div>
    @endif
    <div @class(['p-4' => !$noPadding])>
        {{ $slot }}
    </div>
</div>
