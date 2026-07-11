<?php $__env->startSection('title', 'لوحة تحكم المسؤول | Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-4 mb-5">
    <!-- Dashboard Heading and Breadcrumb Banner -->
    <div class="col-12">
        <div class="dashboard-header d-flex flex-wrap justify-content-between align-items-center gap-3 animate__animated animate__fadeIn">
            <div class="d-flex align-items-center gap-4 position-relative" style="z-index: 1;">
                <div class="bg-white bg-opacity-10 p-3 rounded-circle text-white d-none d-md-block">
                    <i class="fas fa-tachometer-alt fa-2x"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1"><?php echo e(__('app.admin_dashboard_title')); ?></h2>
                    <p class="mb-0 text-white-50 fs-6"><?php echo e(__('app.admin_dashboard_subtitle')); ?></p>
                </div>
            </div>
            <div class="z-1">
                <span class="badge bg-white text-dark p-2 px-3 border shadow-sm rounded-pill font-monospace" style="font-size: 0.88rem;">
                    <i class="fas fa-sync-alt me-1 text-warning spin-icon"></i> <?php echo e(__('app.last_update')); ?> <?php echo e(now()->format('H:i')); ?>

                </span>
            </div>
        </div>
    </div>

    <!-- High-End KPI Statistics Cards with Hover Lifts -->
    <!-- 1. Total Approved Graduates -->
    <div class="col-sm-6 col-lg-4 col-xl-2">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative animate__animated animate__fadeInUp" style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-gold bg-opacity-10 text-gold rounded-4 m-0" style="width: 50px; height: 50px; color: #b89047; background-color: rgba(184, 144, 71, 0.1);">
                        <i class="fas fa-graduation-cap fa-lg"></i>
                    </div>
                </div>
                <h2 class="fw-extrabold mb-1 font-monospace text-dark" style="font-size: 2.2rem;"><?php echo e(number_format($stats['total_approved_graduates'])); ?></h2>
                <p class="mb-0 text-secondary small fw-bold"><?php echo e(__('app.total_approved_graduates')); ?></p>
                <div class="position-absolute bottom-0 end-0" style="opacity: 0.04; pointer-events: none; margin-right:-20px; margin-bottom:-25px;">
                    <i class="fas fa-graduation-cap" style="font-size: 9rem; color: #b89047;"></i>
                </div>
            </div>
            <div style="height: 4px; background: #b89047;"></div>
        </div>
    </div>

    <!-- 2. Total Registered Graduates -->
    <div class="col-sm-6 col-lg-4 col-xl-2">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative animate__animated animate__fadeInUp" style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); animation-delay: 0.05s;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-4 m-0" style="width: 50px; height: 50px;">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                </div>
                <h2 class="fw-extrabold mb-1 font-monospace text-dark" style="font-size: 2.2rem;"><?php echo e(number_format($stats['total_graduates'])); ?></h2>
                <p class="mb-0 text-secondary small fw-bold"><?php echo e(__('app.total_registered_graduates')); ?></p>
                <div class="position-absolute bottom-0 end-0" style="opacity: 0.04; pointer-events: none; margin-right:-20px; margin-bottom:-25px;">
                    <i class="fas fa-users" style="font-size: 9rem;"></i>
                </div>
            </div>
            <div style="height: 4px; background: var(--primary-blue);"></div>
        </div>
    </div>

    <!-- 3. Partner Employers -->
    <div class="col-sm-6 col-lg-4 col-xl-2">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative animate__animated animate__fadeInUp" style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); animation-delay: 0.1s;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-indigo bg-opacity-10 text-indigo rounded-4 m-0" style="width: 50px; height: 50px; color: #6366f1; background-color: rgba(99, 102, 241, 0.1);">
                        <i class="fas fa-building fa-lg"></i>
                    </div>
                </div>
                <h2 class="fw-extrabold mb-1 font-monospace text-dark" style="font-size: 2.2rem;"><?php echo e(number_format($stats['total_employers'])); ?></h2>
                <p class="mb-0 text-secondary small fw-bold"><?php echo e(__('app.total_employers')); ?></p>
                <div class="position-absolute bottom-0 end-0" style="opacity: 0.04; pointer-events: none; margin-right:-20px; margin-bottom:-25px;">
                    <i class="fas fa-building" style="font-size: 9rem; color: #6366f1;"></i>
                </div>
            </div>
            <div style="height: 4px; background: #6366f1;"></div>
        </div>
    </div>

    <!-- 4. Requests Pending Review -->
    <div class="col-sm-6 col-lg-4 col-xl-2">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative animate__animated animate__fadeInUp" style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); animation-delay: 0.15s;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-4 m-0" style="width: 50px; height: 50px;">
                        <i class="fas fa-file-alt fa-lg"></i>
                    </div>
                </div>
                <h2 class="fw-extrabold mb-1 font-monospace text-dark" style="font-size: 2.2rem;"><?php echo e(number_format($stats['pending_requests'])); ?></h2>
                <p class="mb-0 text-secondary small fw-bold"><?php echo e(__('app.requests_pending_review')); ?></p>
                <div class="position-absolute bottom-0 end-0" style="opacity: 0.04; pointer-events: none; margin-right:-20px; margin-bottom:-25px;">
                    <i class="fas fa-file-alt" style="font-size: 9rem;"></i>
                </div>
            </div>
            <div style="height: 4px; background: var(--accent-gold);"></div>
        </div>
    </div>

    <!-- 5. Issued Digital Documents -->
    <div class="col-sm-6 col-lg-4 col-xl-2">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative animate__animated animate__fadeInUp" style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); animation-delay: 0.2s;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-4 m-0" style="width: 50px; height: 50px;">
                        <i class="fas fa-file-signature fa-lg"></i>
                    </div>
                </div>
                <h2 class="fw-extrabold mb-1 font-monospace text-dark" style="font-size: 2.2rem;"><?php echo e(number_format($stats['total_issued_documents'])); ?></h2>
                <p class="mb-0 text-secondary small fw-bold"><?php echo e(__('app.issued_digital_documents')); ?></p>
                <div class="position-absolute bottom-0 end-0" style="opacity: 0.04; pointer-events: none; margin-right:-20px; margin-bottom:-25px;">
                    <i class="fas fa-file-signature" style="font-size: 9rem;"></i>
                </div>
            </div>
            <div style="height: 4px; background: var(--success-green, #10b981);"></div>
        </div>
    </div>

    <!-- 6. Rejected/Revoked Verifications -->
    <div class="col-sm-6 col-lg-4 col-xl-2">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative animate__animated animate__fadeInUp" style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); animation-delay: 0.25s;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-4 m-0" style="width: 50px; height: 50px;">
                        <i class="fas fa-user-shield fa-lg"></i>
                    </div>
                </div>
                <h2 class="fw-extrabold mb-1 font-monospace text-dark" style="font-size: 2.2rem;"><?php echo e(number_format($stats['revoked_documents'])); ?></h2>
                <p class="mb-0 text-secondary small fw-bold"><?php echo e(__('app.rejected_revoked_verifications')); ?></p>
                <div class="position-absolute bottom-0 end-0" style="opacity: 0.04; pointer-events: none; margin-right:-20px; margin-bottom:-25px;">
                    <i class="fas fa-user-shield" style="font-size: 9rem;"></i>
                </div>
            </div>
            <div style="height: 4px; background: var(--danger-red, #ef4444);"></div>
        </div>
    </div>
</div>

<!-- Additional Statistics Row -->
<div class="row g-4 mb-5">
    <!-- 5. Total Faculties -->
    <div class="col-sm-6 col-lg-3">
        <?php if(in_array(Auth::user()->role, ['admin','super_admin'])): ?>
        <a href="<?php echo e(route('admin.faculties.index')); ?>" class="text-decoration-none h-100 d-block">
        <?php endif; ?>
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative animate__animated animate__fadeInUp" style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-info bg-opacity-10 text-info rounded-4 m-0" style="width: 50px; height: 50px;">
                        <i class="fas fa-university fa-lg"></i>
                    </div>
                </div>
                <h2 class="fw-extrabold mb-1 font-monospace text-dark" style="font-size: 2.2rem;"><?php echo e(number_format($stats['total_faculties'])); ?></h2>
                <p class="mb-0 text-secondary small fw-bold"><?php echo e(__('app.total_faculties')); ?></p>
                <div class="position-absolute bottom-0 end-0" style="opacity: 0.04; pointer-events: none; margin-right:-20px; margin-bottom:-25px;">
                    <i class="fas fa-university" style="font-size: 9rem;"></i>
                </div>
            </div>
            <div style="height: 4px; background: var(--info-blue, #0284c7);"></div>
        </div>
        <?php if(in_array(Auth::user()->role, ['admin','super_admin'])): ?>
        </a>
        <?php endif; ?>
    </div>

    <!-- 6. Total Majors -->
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative animate__animated animate__fadeInUp" style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); animation-delay: 0.1s;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-purple bg-opacity-10 text-purple rounded-4 m-0" style="width: 50px; height: 50px;">
                        <i class="fas fa-book fa-lg"></i>
                    </div>
                </div>
                <h2 class="fw-extrabold mb-1 font-monospace text-dark" style="font-size: 2.2rem;"><?php echo e(number_format($stats['total_majors'])); ?></h2>
                <p class="mb-0 text-secondary small fw-bold"><?php echo e(__('app.total_majors')); ?></p>
                <div class="position-absolute bottom-0 end-0" style="opacity: 0.04; pointer-events: none; margin-right:-20px; margin-bottom:-25px;">
                    <i class="fas fa-book" style="font-size: 9rem;"></i>
                </div>
            </div>
            <div style="height: 4px; background: #7c3aed;"></div>
        </div>
    </div>

    <!-- 7. Total Document Requests -->
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative animate__animated animate__fadeInUp" style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); animation-delay: 0.2s;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-4 m-0" style="width: 50px; height: 50px;">
                        <i class="fas fa-file-invoice fa-lg"></i>
                    </div>
                </div>
                <h2 class="fw-extrabold mb-1 font-monospace text-dark" style="font-size: 2.2rem;"><?php echo e(number_format($stats['total_document_requests'])); ?></h2>
                <p class="mb-0 text-secondary small fw-bold"><?php echo e(__('app.total_document_requests')); ?></p>
                <div class="position-absolute bottom-0 end-0" style="opacity: 0.04; pointer-events: none; margin-right:-20px; margin-bottom:-25px;">
                    <i class="fas fa-file-invoice" style="font-size: 9rem;"></i>
                </div>
            </div>
            <div style="height: 4px; background: var(--primary-blue);"></div>
        </div>
    </div>

    <!-- 8. Active Jobs -->
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative animate__animated animate__fadeInUp" style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); animation-delay: 0.3s;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-4 m-0" style="width: 50px; height: 50px;">
                        <i class="fas fa-briefcase fa-lg"></i>
                    </div>
                </div>
                <h2 class="fw-extrabold mb-1 font-monospace text-dark" style="font-size: 2.2rem;"><?php echo e(number_format($stats['total_active_jobs'])); ?></h2>
                <p class="mb-0 text-secondary small fw-bold"><?php echo e(__('app.total_active_jobs')); ?></p>
                <div class="position-absolute bottom-0 end-0" style="opacity: 0.04; pointer-events: none; margin-right:-20px; margin-bottom:-25px;">
                    <i class="fas fa-briefcase" style="font-size: 9rem;"></i>
                </div>
            </div>
            <div style="height: 4px; background: var(--success-green, #10b981);"></div>
        </div>
    </div>

    <!-- 9. Pending Signatures -->
    <div class="col-sm-6 col-lg-3">
        <a href="<?php echo e(route('admin.pending-signatures')); ?>" class="text-decoration-none h-100 d-block">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative animate__animated animate__fadeInUp" style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); animation-delay: 0.35s;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-4 m-0" style="width: 50px; height: 50px;">
                        <i class="fas fa-file-signature fa-lg"></i>
                    </div>
                    <?php if($stats['pending_signatures'] > 0): ?>
                        <span class="badge bg-danger rounded-pill px-2"><?php echo e($stats['pending_signatures']); ?></span>
                    <?php endif; ?>
                </div>
                <h2 class="fw-extrabold mb-1 font-monospace text-dark" style="font-size: 2.2rem;"><?php echo e(number_format($stats['pending_signatures'])); ?></h2>
                <p class="mb-0 text-secondary small fw-bold">توقيعات معلقة</p>
                <div class="position-absolute bottom-0 end-0" style="opacity: 0.04; pointer-events: none; margin-right:-20px; margin-bottom:-25px;">
                    <i class="fas fa-file-signature" style="font-size: 9rem;"></i>
                </div>
            </div>
            <div style="height: 4px; background: #f59e0b;"></div>
        </div>
        </a>
    </div>

    <!-- 10. Completed Signatures -->
    <div class="col-sm-6 col-lg-3">
        <a href="<?php echo e(route('admin.ready-signatures')); ?>" class="text-decoration-none h-100 d-block">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative animate__animated animate__fadeInUp" style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); animation-delay: 0.4s;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-4 m-0" style="width: 50px; height: 50px;">
                        <i class="fas fa-check-double fa-lg"></i>
                    </div>
                </div>
                <h2 class="fw-extrabold mb-1 font-monospace text-dark" style="font-size: 2.2rem;"><?php echo e(number_format($stats['completed_signatures'])); ?></h2>
                <p class="mb-0 text-secondary small fw-bold">توقيعات جاهزة</p>
                <div class="position-absolute bottom-0 end-0" style="opacity: 0.04; pointer-events: none; margin-right:-20px; margin-bottom:-25px;">
                    <i class="fas fa-check-double" style="font-size: 9rem;"></i>
                </div>
            </div>
            <div style="height: 4px; background: #16a34a;"></div>
        </div>
        </a>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- Main Charts Panel (Left Columns) -->
    <div class="col-lg-8">
        <!-- 1. Request Vitality (Timeline Line Chart) -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white p-4 border-0 pb-1 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-chart-line text-primary"></i>
                    <span><?php echo e(__('app.requests_vitality')); ?></span>
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="chart-container position-relative" style="height: 300px; width: 100%;">
                    <canvas id="monthlyRequestsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 2. Dual Sub-Charts (Grid-based breakdown) -->
        <div class="row g-4">
            <!-- Top Document Types (Horizontal Bar Chart) -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                    <div class="card-header bg-white p-4 border-0 pb-1">
                        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="fas fa-chart-bar text-warning"></i>
                            <span><?php echo e(__('app.most_requested_documents')); ?></span>
                        </h5>
                    </div>
                    <div class="card-body p-4 pt-1">
                        <div class="chart-container position-relative" style="height: 250px; width: 100%;">
                            <canvas id="topTypesBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Status Distribution (Polar Area Chart) -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                    <div class="card-header bg-white p-4 border-0 pb-1">
                        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="fas fa-chart-pie text-success"></i>
                            <span><?php echo e(__('app.current_status_distribution')); ?></span>
                        </h5>
                    </div>
                    <div class="card-body p-4 pt-1">
                        <div class="chart-container position-relative" style="height: 250px; width: 100%;">
                            <canvas id="statusPolarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Analytics Widget Area (Right Columns) -->
    <div class="col-lg-4">
        <!-- Requests by Major (Donut Chart) -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white p-4 border-0 pb-1">
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-graduation-cap text-primary"></i>
                    <span><?php echo e(__('app.requests_by_major')); ?></span>
                </h5>
            </div>
            <div class="card-body p-4 text-center">
                <div class="chart-container position-relative" style="height: 280px; width: 100%;">
                    <canvas id="majorDonutChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Latest Received Requests (SaaS-styled List) -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-gradient p-4 border-0 text-white" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);">
                <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-file-invoice text-warning"></i>
                    <span><?php echo e(__('app.latest_received_requests')); ?></span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $recentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $statusClass = 'ds-status-default';
                            if($request->status == 'SUBMITTED') $statusClass = 'ds-status-submitted';
                            elseif($request->status == 'UNDER_REVIEW') $statusClass = 'ds-status-under_review';
                            elseif($request->status == 'APPROVED') $statusClass = 'ds-status-approved';
                            elseif($request->status == 'PENDING_SIGNATURES') $statusClass = 'ds-status-pending_signatures';
                            elseif($request->status == 'READY') $statusClass = 'ds-status-ready';
                            elseif($request->status == 'ISSUED') $statusClass = 'ds-status-issued';
                            elseif($request->status == 'REJECTED') $statusClass = 'ds-status-rejected';
                        ?>
                        <div class="list-group-item p-3 border-0 border-bottom d-flex justify-content-between align-items-center" style="transition: background-color 0.2s ease;">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.92rem;"><?php echo e($request->user->name); ?></h6>
                                <span class="text-secondary small d-flex align-items-center gap-1">
                                    <i class="far fa-file-alt text-muted small"></i> 
                                    <?php echo e(app()->getLocale() == 'ar' ? $request->documentType->name_ar : $request->documentType->name_en); ?>

                                </span>
                            </div>
                            <span class="ds-status-badge <?php echo e($statusClass); ?>" style="font-size: 0.72rem; padding: 0.25rem 0.65rem;">
                                <?php echo e(__('app.document_status.' . $request->status) ?? $request->status); ?>

                            </span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                            <p class="mb-0 small"><?php echo e(__('app.no_recent_requests')); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="p-3 text-center bg-light">
                    <a href="<?php echo e(route('admin.requests.index')); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-bold">
                        <?php echo e(__('app.view_all_requests')); ?> <i class="fas fa-arrow-left ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Premium Call to Action (Analytics & Detailed Reports) -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-gradient text-white p-4 p-md-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%); border-bottom: 5px solid var(--accent-gold);">
            <div class="position-absolute top-0 start-0 w-100 h-100 opacity-5 pointer-events-none" style="background-image: radial-gradient(circle at 10% 20%, #b89047 1px, transparent 1px); background-size: 15px 15px;"></div>
            
            <div class="row align-items-center z-1 position-relative">
                <div class="col-md-9 text-start">
                    <h3 class="fw-extrabold text-white mb-2" style="font-size: 1.75rem;"><?php echo e(__('app.need_detailed_reports')); ?></h3>
                    <p class="mb-0 opacity-85 leading-relaxed" style="max-width: 800px; font-size: 0.95rem;"><?php echo e(__('app.advanced_reports_desc')); ?></p>
                </div>
                <div class="col-md-3 text-md-end mt-4 mt-md-0">
                    <a href="<?php echo e(route('admin.reports.graduates')); ?>" class="btn btn-gradient rounded-pill px-4 py-3 fw-bold text-white shadow-lg d-flex align-items-center justify-content-center gap-2">
                        <span><?php echo e(__('app.go_to_reports')); ?></span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.spin-icon {
    display: inline-block;
}
.card:hover .spin-icon {
    animation: spin 1.5s linear infinite;
}
.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 35px rgba(11, 37, 69, 0.08) !important;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Theme Harmonious Color Palette matching university branding
    const COLORS = [
        '#134074', // Brand Deep Navy Accent
        '#b89047', // Heritage Prestige Gold
        '#10b981', // Success Green
        '#f59e0b', // Warning Yellow
        '#ef4444', // Danger Red
        '#6366f1', // Indigo Accent
        '#0284c7'  // Light Blue Accent
    ];
    
    // 1. Monthly Line Chart
    new Chart(document.getElementById('monthlyRequestsChart'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: '<?php echo e(__('app.document_requests_chart')); ?>',
                data: <?php echo json_encode($requestCounts); ?>,
                borderColor: '#134074',
                backgroundColor: 'rgba(19, 64, 116, 0.05)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#fff',
                pointHoverBackgroundColor: '#b89047',
                pointBorderWidth: 2,
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    padding: 10,
                    cornerRadius: 8,
                    backgroundColor: '#061121',
                    titleFont: { size: 13, weight: 'bold' }
                }
            },
            scales: {
                y: { grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false }, beginAtZero: true },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Top Document Types Bar Chart
    new Chart(document.getElementById('topTypesBarChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($topTypes->pluck('label')); ?>,
            datasets: [{
                data: <?php echo json_encode($topTypes->pluck('value')); ?>,
                backgroundColor: COLORS,
                borderRadius: 6,
                barThickness: 16
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false } },
                y: { grid: { display: false } }
            }
        }
    });

    // 3. Status Polar Chart
    new Chart(document.getElementById('statusPolarChart'), {
        type: 'polarArea',
        data: {
            labels: <?php echo json_encode($statusBreakdown->pluck('label')); ?>,
            datasets: [{
                data: <?php echo json_encode($statusBreakdown->pluck('value')); ?>,
                backgroundColor: [
                    'rgba(19, 64, 116, 0.12)',
                    'rgba(184, 144, 71, 0.12)',
                    'rgba(16, 185, 129, 0.12)',
                    'rgba(245, 158, 11, 0.12)',
                    'rgba(239, 68, 68, 0.12)'
                ],
                borderColor: [
                    '#134074',
                    '#b89047',
                    '#10b981',
                    '#f59e0b',
                    '#ef4444'
                ],
                borderWidth: 1.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { 
                    position: 'bottom', 
                    labels: { padding: 12, usePointStyle: true, boxWidth: 10 } 
                } 
            },
            scales: { r: { ticks: { display: false }, grid: { color: 'rgba(0,0,0,0.04)' } } }
        }
    });

    // 4. Major Donut Chart
    new Chart(document.getElementById('majorDonutChart'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($majorStats->pluck('label')); ?>,
            datasets: [{
                data: <?php echo json_encode($majorStats->pluck('value')); ?>,
                backgroundColor: COLORS,
                borderWidth: 2,
                borderColor: '#ffffff',
                cutout: '72%',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom', 
                    labels: { padding: 12, boxWidth: 10, usePointStyle: true, font: { size: 11 } } 
                }
            }
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\RTX\Desktop\myproject\ملفات المشروع\المعدلهgraduates-portal3.22\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>