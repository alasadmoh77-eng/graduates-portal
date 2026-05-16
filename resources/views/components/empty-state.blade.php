{{--
    x-empty-state — Reusable empty state placeholder
    Props:
      $icon     : FontAwesome icon class (e.g. 'fa-inbox')
      $title    : Main heading text
      $message  : Helper subtext
      $action   : (optional) URL for CTA button
      $actionLabel : (optional) CTA button label
--}}
@props([
    'icon'        => 'fa-folder-open',
    'title'       => __('app.no_results'),
    'message'     => '',
    'action'      => null,
    'actionLabel' => __('app.go_back'),
])

<div class="ds-empty-state">
    <div class="ds-empty-icon">
        <i class="fas {{ $icon }}"></i>
    </div>
    <h5>{{ $title }}</h5>
    @if($message)
        <p>{{ $message }}</p>
    @endif
    @if($action)
        <a href="{{ $action }}" class="btn btn-primary rounded-pill px-4 mt-2">
            {{ $actionLabel }}
        </a>
    @endif
</div>
