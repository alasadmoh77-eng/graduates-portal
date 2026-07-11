@extends('layouts.app')
@section('title', 'تحليلات التوظيف')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">تحليلات التوظيف</h1>
</div>

{{-- Top KPIs --}}
<div class="row g-4 mb-5">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 text-center py-4 px-3">
            <div class="display-6 mb-1">🎓</div>
            <h2 class="fw-bold mb-1">{{ $totalGraduates }}</h2>
            <p class="text-muted small mb-0">إجمالي الخريجين</p>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 text-center py-4 px-3">
            <div class="display-6 mb-1">✅</div>
            <h2 class="fw-bold mb-1 text-success">{{ $totalHired }}</h2>
            <p class="text-muted small mb-0">موظَّف</p>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 text-center py-4 px-3">
            <div class="display-6 mb-1">📊</div>
            <h2 class="fw-bold mb-1 text-primary">{{ $employmentRate }}%</h2>
            <p class="text-muted small mb-0">نسبة التوظيف</p>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 text-center py-4 px-3">
            <div class="display-6 mb-1">💼</div>
            <h2 class="fw-bold mb-1 text-info">{{ $activeJobs }}</h2>
            <p class="text-muted small mb-0">وظائف نشطة</p>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Application Funnel --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold mb-4">مسار الطلبات</h5>
            @php
                $funnelSteps = [
                    'total'       => ['label' => 'إجمالي الطلبات', 'color' => 'primary', 'icon' => 'fas fa-inbox'],
                    'shortlisted' => ['label' => 'مختصرة', 'color' => 'info', 'icon' => 'fas fa-list-check'],
                    'interviewed' => ['label' => 'مقابلة', 'color' => 'warning', 'icon' => 'fas fa-comments'],
                    'hired'       => ['label' => 'موظّف', 'color' => 'success', 'icon' => 'fas fa-user-check'],
                ];
            @endphp
            @foreach($funnelSteps as $key => $meta)
                @php $val = $funnel[$key]; $pct = $funnel['total'] > 0 ? round($val / $funnel['total'] * 100) : 0; @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-semibold"><i class="{{ $meta['icon'] }} me-2 text-{{ $meta['color'] }}"></i>{{ $meta['label'] }}</span>
                        <span class="fw-bold">{{ $val }}</span>
                    </div>
                    <div class="progress rounded-pill" style="height: 10px;">
                        <div class="progress-bar bg-{{ $meta['color'] }} rounded-pill" style="width: {{ $pct }}%;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Top Employers --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold mb-4">أكثر جهات التوظيف توظيفاً</h5>
            @if($topEmployers->isEmpty())
                <p class="text-muted text-center py-4">لا توجد بيانات بعد.</p>
            @else
                @foreach($topEmployers as $i => $emp)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                             style="width:36px;height:36px;font-size:0.85rem;">{{ $i + 1 }}</div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $emp->company_name }}</div>
                            <div class="progress rounded-pill mt-1" style="height: 6px;">
                                <div class="progress-bar bg-success rounded-pill"
                                     style="width: {{ $topEmployers->first()->hire_count > 0 ? round($emp->hire_count / $topEmployers->first()->hire_count * 100) : 0 }}%;"></div>
                            </div>
                        </div>
                        <span class="badge bg-success rounded-pill px-3">{{ $emp->hire_count }} توظيف</span>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Employment Rate by Major --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-4">نسبة التوظيف حسب التخصص</h5>
            @if($byMajor->isEmpty())
                <p class="text-muted text-center py-4">لا توجد بيانات بعد.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>التخصص</th>
                                <th>إجمالي الخريجين</th>
                                <th>موظَّف</th>
                                <th>نسبة التوظيف</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byMajor as $row)
                                <tr>
                                    <td class="fw-semibold">{{ $row->name_ar }}</td>
                                    <td>{{ $row->total }}</td>
                                    <td class="text-success fw-semibold">{{ $row->hired }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1 rounded-pill" style="height: 8px;">
                                                <div class="progress-bar bg-{{ $row->rate >= 50 ? 'success' : ($row->rate >= 25 ? 'warning' : 'danger') }} rounded-pill"
                                                     style="width: {{ $row->rate }}%;"></div>
                                            </div>
                                            <span class="small fw-bold text-nowrap">{{ $row->rate }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
