{{--
    x-dashboard-card — KPI / stat card for dashboards
    Props:
      $value      : The numeric/text value to display prominently
      $label      : Description of the metric
      $icon       : FontAwesome icon class (e.g. 'fa-users')
      $iconBg     : Bootstrap bg utility for icon background (e.g. 'bg-primary')
      $iconColor  : Bootstrap text utility for icon color (e.g. 'text-primary')
      $trend      : (optional) trend text shown below value
      $trendColor : (optional) Bootstrap text color for trend (default: 'text-muted')
--}}
@props([
    'value'      => '0',
    'label'      => '',
    'icon'       => 'fa-circle',
    'iconBg'     => 'bg-primary',
    'iconColor'  => 'text-primary',
    'trend'      => '',
    'trendColor' => 'text-muted',
])

<div class="ds-stat-card p-4">
    <div class="d-flex align-items-start gap-3 mb-3">
        <div class="ds-stat-icon {{ $iconBg }} bg-opacity-10 {{ $iconColor }}">
            <i class="fas {{ $icon }}"></i>
        </div>
    </div>
    <h3 class="fw-bold mb-1">{{ $value }}</h3>
    <p class="mb-0 text-muted small">{{ $label }}</p>
    @if($trend)
        <div class="small {{ $trendColor }} mt-1 fw-bold">{{ $trend }}</div>
    @endif
</div>
