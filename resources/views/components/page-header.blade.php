{{--
    x-page-header — Standard page header with title, subtitle, and optional action slot
    Props:
      $title    : Page heading
      $subtitle : (optional) Muted descriptive text
      $icon     : (optional) FontAwesome icon class shown before title
--}}
@props([
    'title'    => '',
    'subtitle' => '',
    'icon'     => '',
])

<div class="ds-page-header">
    <div>
        <h2 class="fw-bold text-primary mb-1">
            @if($icon)
                <i class="fas {{ $icon }} me-2"></i>
            @endif
            {{ $title }}
        </h2>
        @if($subtitle)
            <p class="text-muted mb-0 small">{{ $subtitle }}</p>
        @endif
    </div>
    @if($slot->isNotEmpty())
        <div class="d-flex align-items-center gap-2">
            {{ $slot }}
        </div>
    @endif
</div>
