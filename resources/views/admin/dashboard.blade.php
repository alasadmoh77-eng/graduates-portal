@extends('layouts.app')

@section('title', 'لوحة تحكم المسؤول')

@section('content')
<div class="row g-4 mb-5">
    <div class="col-12">
        <x-page-header 
            :title="__('app.admin_dashboard_title')"
            :subtitle="__('app.admin_dashboard_subtitle')"
            icon="fa-tachometer-alt"
        >
            <span class="badge bg-light text-dark p-2 border shadow-sm">
                <i class="fas fa-clock me-1 text-primary"></i> {{ __('app.last_update') }} {{ now()->format('H:i') }}
            </span>
        </x-page-header>
    </div>

    <!-- KPI Cards: More Professional -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                        <i class="fas fa-users text-primary fa-2x"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1">{{ number_format($stats['total_graduates']) }}</h4>
                <p class="mb-0 text-muted small">{{ __('app.total_registered_graduates') }}</p>
                <div class="position-absolute bottom-0 end-0 opacity-10" style="margin-right:-10px; margin-bottom:-20px;">
                    <i class="fas fa-users" style="font-size: 8rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                        <i class="fas fa-file-alt text-warning fa-2x"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1">{{ number_format($stats['pending_requests']) }}</h4>
                <p class="mb-0 text-muted small">{{ __('app.requests_pending_review') }}</p>
                <div class="position-absolute bottom-0 end-0 opacity-10" style="margin-right:-10px; margin-bottom:-20px;">
                    <i class="fas fa-file-alt" style="font-size: 8rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                        <i class="fas fa-file-signature text-success fa-2x"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1">{{ number_format($stats['issued_requests']) }}</h4>
                <p class="mb-0 text-muted small">{{ __('app.issued_digital_documents') }}</p>
                <div class="position-absolute bottom-0 end-0 opacity-10" style="margin-right:-10px; margin-bottom:-20px;">
                    <i class="fas fa-file-signature" style="font-size: 8rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger">
                        <i class="fas fa-user-shield text-danger fa-2x"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1">{{ number_format($stats['revoked_documents']) }}</h4>
                <p class="mb-0 text-muted small">{{ __('app.rejected_revoked_verifications') }}</p>
                <div class="position-absolute bottom-0 end-0 opacity-10" style="margin-right:-10px; margin-bottom:-20px;">
                    <i class="fas fa-user-shield" style="font-size: 8rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- Main Charts -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white p-4 border-0">
                <h5 class="fw-bold mb-0 text-dark">{{ __('app.requests_vitality') }}</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <canvas id="monthlyRequestsChart" height="280"></canvas>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white p-4 border-0">
                        <h5 class="fw-bold mb-0 text-dark">{{ __('app.most_requested_documents') }}</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <canvas id="topTypesBarChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white p-4 border-0">
                        <h5 class="fw-bold mb-0 text-dark">{{ __('app.current_status_distribution') }}</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <canvas id="statusPolarChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Charts & Info -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white p-4 border-0">
                <h5 class="fw-bold mb-0 text-dark">{{ __('app.requests_by_major') }}</h5>
            </div>
            <div class="card-body p-4 pt-0 text-center">
                <div style="height: 300px;">
                    <canvas id="majorDonutChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-dark text-white p-4 border-0">
                <h5 class="fw-bold mb-0">{{ __('app.latest_received_requests') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recentRequests as $request)
                        <div class="list-group-item p-3 border-0 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $request->user->name }}</h6>
                                    <span class="text-muted small">{{ $request->documentType->name_ar }}</span>
                                </div>
                                <span class="badge bg-light text-dark border p-1 px-2">{{ $request->status }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center p-4 mb-0 text-muted">{{ __('app.no_recent_requests') }}</p>
                    @endforelse
                </div>
                <div class="p-3 text-center bg-light">
                    <a href="{{ route('admin.requests.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">{{ __('app.view_all_requests') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-md-9">
                    <h3 class="fw-bold mb-2">{{ __('app.need_detailed_reports') }}</h3>
                    <p class="mb-0 opacity-75">{{ __('app.advanced_reports_desc') }}</p>
                </div>
                <div class="col-md-3 text-md-end mt-4 mt-md-0">
                    <a href="{{ route('admin.reports.graduates') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-primary">{{ __('app.go_to_reports') }} <i class="fas fa-arrow-circle-left ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const COLORS = ['#4361ee', '#4cc9f0', '#4895ef', '#3f37c9', '#560bad', '#b5179e', '#f72585'];
    
    // 1. Monthly Line Chart
    new Chart(document.getElementById('monthlyRequestsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: '{{ __('app.document_requests_chart') }}',
                data: {!! json_encode($requestCounts) !!},
                borderColor: '#4361ee',
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false }, beginAtZero: true },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Top Document Types Bar Chart
    new Chart(document.getElementById('topTypesBarChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topTypes->pluck('label')) !!},
            datasets: [{
                data: {!! json_encode($topTypes->pluck('value')) !!},
                backgroundColor: COLORS,
                borderRadius: 8
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false } },
                y: { grid: { display: false } }
            }
        }
    });

    // 3. Status Polar Chart
    new Chart(document.getElementById('statusPolarChart'), {
        type: 'polarArea',
        data: {
            labels: {!! json_encode($statusBreakdown->pluck('label')) !!},
            datasets: [{
                data: {!! json_encode($statusBreakdown->pluck('value')) !!},
                backgroundColor: ['#4361ee33', '#4cc9f033', '#4895ef33', '#3f37c933', '#560bad33'],
                borderColor: ['#4361ee', '#4cc9f0', '#4895ef', '#3f37c9', '#560bad'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true } } },
            scales: { r: { ticks: { display: false } } }
        }
    });

    // 4. Major Donut Chart
    new Chart(document.getElementById('majorDonutChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($majorStats->pluck('label')) !!},
            datasets: [{
                data: {!! json_encode($majorStats->pluck('value')) !!},
                backgroundColor: COLORS,
                borderWidth: 0,
                cutout: '75%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 15, boxWidth: 12, usePointStyle: true } }
            }
        }
    });
</script>
@endsection
