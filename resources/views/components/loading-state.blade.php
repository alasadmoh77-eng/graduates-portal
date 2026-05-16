{{--
    x-loading-state — Lightweight skeleton loading placeholder
    Props:
      $rows : number of skeleton rows to show (default: 3)
      $type : 'rows'|'cards' (default: 'rows')
--}}
@props(['rows' => 3, 'type' => 'rows'])

@if($type === 'cards')
    <div class="row g-4">
        @for($i = 0; $i < $rows; $i++)
            <div class="col-md-4">
                <div class="ds-card p-4">
                    <div class="ds-skeleton ds-skeleton-text wide mb-3" style="height:1.5rem;"></div>
                    <div class="ds-skeleton ds-skeleton-text medium mb-2"></div>
                    <div class="ds-skeleton ds-skeleton-text short"></div>
                </div>
            </div>
        @endfor
    </div>
@else
    <div class="ds-card p-4">
        @for($i = 0; $i < $rows; $i++)
            <div class="d-flex gap-3 mb-4 align-items-center">
                <div class="ds-skeleton rounded-circle flex-shrink-0" style="width:40px;height:40px;"></div>
                <div class="flex-grow-1">
                    <div class="ds-skeleton ds-skeleton-text wide mb-2"></div>
                    <div class="ds-skeleton ds-skeleton-text medium"></div>
                </div>
            </div>
        @endfor
    </div>
@endif
