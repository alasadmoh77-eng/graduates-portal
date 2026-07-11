
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'icon'        => 'fa-folder-open',
    'title'       => __('app.no_results'),
    'message'     => '',
    'action'      => null,
    'actionLabel' => __('app.go_back'),
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'icon'        => 'fa-folder-open',
    'title'       => __('app.no_results'),
    'message'     => '',
    'action'      => null,
    'actionLabel' => __('app.go_back'),
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="ds-empty-state">
    <div class="ds-empty-icon">
        <i class="fas <?php echo e($icon); ?>"></i>
    </div>
    <h5><?php echo e($title); ?></h5>
    <?php if($message): ?>
        <p><?php echo e($message); ?></p>
    <?php endif; ?>
    <?php if($action): ?>
        <a href="<?php echo e($action); ?>" class="btn btn-primary rounded-pill px-4 mt-2">
            <?php echo e($actionLabel); ?>

        </a>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\RTX\Desktop\myproject\ملفات المشروع\المعدلهgraduates-portal3.22\resources\views/components/empty-state.blade.php ENDPATH**/ ?>