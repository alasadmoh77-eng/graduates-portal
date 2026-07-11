<?php $__env->startSection('title', __('app.admin_requests_index_title')); ?>


<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4 px-lg-5">
    
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-primary mb-1"><i class="fas fa-inbox me-2"></i> <?php echo e(__('app.admin_requests_index_heading')); ?></h2>
            <p class="text-muted mb-0">لوحة التحكم المركزية لمعالجة وإصدار وثائق الخريجين.</p>
        </div>
        <div class="bg-white px-4 py-2 rounded-pill shadow-sm border text-primary fw-bold">
            إجمالي الطلبات: <span class="badge bg-primary rounded-pill ms-1"><?php echo e($requests->total()); ?></span>
        </div>
    </div>

    <!-- Enhanced Filters -->
    <div class="filter-card mb-4">
        <div class="card-header bg-white border-bottom p-3">
            <h6 class="fw-bold mb-0 text-secondary"><i class="fas fa-filter me-2"></i> أدوات التصفية والبحث</h6>
        </div>
        <div class="card-body p-4">
            <form action="<?php echo e(route('admin.requests.index')); ?>" method="GET" class="row g-3">
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <label class="form-label small fw-bold text-muted">البحث العام (الاسم، الرمز)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control custom-input border-start-0 ps-0" value="<?php echo e(request('search')); ?>" placeholder="<?php echo e(__('app.admin_filter_search_placeholder')); ?>">
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6">
                    <label class="form-label small fw-bold text-muted"><?php echo e(__('app.status')); ?></label>
                    <select name="status" class="form-select custom-input">
                        <option value=""><?php echo e(__('app.admin_filter_all_statuses')); ?></option>
                        <?php $__currentLoopData = ['SUBMITTED', 'UNDER_REVIEW', 'APPROVED', 'PENDING_SIGNATURES', 'REJECTED', 'READY', 'ISSUED']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($status); ?>" <?php echo e(request('status') == $status ? 'selected' : ''); ?>><?php echo e(__('app.document_status.'.$status)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6">
                    <label class="form-label small fw-bold text-muted"><?php echo e(__('app.documents_document_type')); ?></label>
                    <select name="document_type_id" class="form-select custom-input">
                        <option value=""><?php echo e(__('app.admin_filter_all_types')); ?></option>
                        <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($type->id); ?>" <?php echo e(request('document_type_id') == $type->id ? 'selected' : ''); ?>><?php echo e($type->name_ar); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-xl-1 col-lg-4 col-md-6">
                    <label class="form-label small fw-bold text-muted"><?php echo e(__('app.language')); ?></label>
                    <select name="language" class="form-select custom-input">
                        <option value="">الكل</option>
                        <option value="AR" <?php echo e(request('language') === 'AR' ? 'selected' : ''); ?>>العربية (AR)</option>
                        <option value="EN" <?php echo e(request('language') === 'EN' ? 'selected' : ''); ?>>الإنكليزية (EN)</option>
                    </select>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6">
                    <label class="form-label small fw-bold text-muted">من تاريخ</label>
                    <input type="text" name="date_from" class="form-control custom-input date-picker-input" value="<?php echo e(request('date_from')); ?>" placeholder="YYYY-MM-DD" dir="ltr" lang="en" readonly autocomplete="off">
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6">
                    <label class="form-label small fw-bold text-muted">إلى تاريخ</label>
                    <div class="d-flex gap-2">
                        <input type="text" name="date_to" class="form-control custom-input date-picker-input" value="<?php echo e(request('date_to')); ?>" placeholder="YYYY-MM-DD" dir="ltr" lang="en" readonly autocomplete="off">
                        <button type="submit" class="filter-btn flex-shrink-0" title="تطبيق التصفية"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table custom-table table-hover align-middle mb-0 text-center">
                <thead>
                    <tr>
                        <th class="text-start ps-4"><?php echo e(__('app.documents_col_tracking')); ?></th>
                        <th class="text-start"><?php echo e(__('app.admin_col_graduate')); ?></th>
                        <th><?php echo e(__('app.documents_col_type')); ?></th>
                        <th><?php echo e(__('app.language')); ?></th>
                        <th><?php echo e(__('app.status')); ?></th>
                        <th><?php echo e(__('app.admin_col_submitted')); ?></th>
                        <th class="text-end pe-4"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="text-start ps-4">
                                <div class="font-monospace fw-bold text-primary bg-light px-2 py-1 rounded d-inline-block"><?php echo e($request->tracking_code); ?></div>
                            </td>
                            <td class="text-start">
                                <div class="text-dark fw-bold d-flex align-items-center gap-2">
                                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <div>
                                        <div class="mb-0"><?php echo e($request->user->name); ?></div>
                                        <div class="small text-muted fw-normal"><?php echo e($request->user->graduate->university_id ?? 'بدون رقم'); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark small">
                                    <i class="fas <?php echo e($request->documentType->code === 'ACADEMIC_RECORD' ? 'fa-graduation-cap' : 'fa-award'); ?> text-secondary me-1"></i>
                                    <?php echo e($request->documentType->name_ar); ?>

                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1"><?php echo e($request->language); ?></span>
                            </td>
                            <td>
                                <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $request->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($request->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                            </td>
                            <td>
                                <div class="small fw-bold text-muted"><?php echo e($request->created_at->format('Y-m-d')); ?></div>
                            </td>
                            <td class="text-end pe-4">
                                <a href="<?php echo e(route('admin.requests.show', $request)); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-bold">
                                    معالجة <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="p-0">
                                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'fa-inbox','title' => __('app.no_recent_requests'),'message' => __('app.no_requests_match')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'fa-inbox','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('app.no_recent_requests')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('app.no_requests_match'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($requests->hasPages()): ?>
            <div class="card-footer bg-white p-4 d-flex justify-content-center border-top">
                <?php echo e($requests->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\RTX\Desktop\myproject\ملفات المشروع\المعدلهgraduates-portal3.22\resources\views/admin/requests/index.blade.php ENDPATH**/ ?>