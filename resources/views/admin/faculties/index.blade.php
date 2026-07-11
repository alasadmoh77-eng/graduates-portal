@extends('layouts.app')

@section('title', __('app.faculty_management'))

@section('styles')
<style>
    .status-badge { font-size: 0.72rem; padding: 0.3rem 0.7rem; border-radius: 50px; font-weight: 700; letter-spacing: 0.5px; }
    .status-active   { background: #dcfce7; color: #166534; }
    .status-inactive { background: #fee2e2; color: #991b1b; }
    .table-hover tbody tr:hover { background-color: #f8fafc; }
    .faculty-avatar {
        width: 42px; height: 42px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 1.1rem; color: white;
        background: linear-gradient(135deg, #134074, #1d6fa4);
        flex-shrink: 0;
    }
</style>
@endsection

@section('content')
<div class="row g-4">

    {{-- Page Header --}}
    <div class="col-12">
        <div class="dashboard-header d-flex flex-wrap justify-content-between align-items-center gap-3 animate__animated animate__fadeIn">
            <div class="d-flex align-items-center gap-4 position-relative" style="z-index: 1;">
                <div class="bg-white bg-opacity-10 p-3 rounded-circle text-white d-none d-md-block">
                    <i class="fas fa-university fa-2x"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">{{ __('app.faculty_management') }}</h2>
                    <p class="mb-0 text-white-50 fs-6">{{ __('app.faculty_management_subtitle') }}</p>
                </div>
            </div>
            <div class="z-1">
                <a href="{{ route('admin.faculties.create') }}" class="btn btn-gradient rounded-pill px-4 py-2 fw-bold text-white shadow">
                    <i class="fas fa-plus me-2"></i>{{ __('app.add_new_faculty') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Summary KPI Cards --}}
    @php
        $totalFaculties = $faculties->total();
        $activeFaculties = \App\Models\Faculty::where('status', 'active')->count();
        $inactiveFaculties = \App\Models\Faculty::where('status', 'inactive')->count();
    @endphp
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-4" style="width:50px;height:50px;">
                    <i class="fas fa-university fa-lg"></i>
                </div>
                <div>
                    <h3 class="fw-extrabold mb-0 font-monospace">{{ $totalFaculties }}</h3>
                    <p class="text-secondary small fw-bold mb-0">{{ __('app.total_faculties') }}</p>
                </div>
            </div>
            <div style="height:3px;background:var(--primary-blue)"></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="icon-box bg-success bg-opacity-10 text-success rounded-4" style="width:50px;height:50px;">
                    <i class="fas fa-check-circle fa-lg"></i>
                </div>
                <div>
                    <h3 class="fw-extrabold mb-0 font-monospace">{{ $activeFaculties }}</h3>
                    <p class="text-secondary small fw-bold mb-0">{{ __('app.active_faculties') }}</p>
                </div>
            </div>
            <div style="height:3px;background:#10b981"></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-4" style="width:50px;height:50px;">
                    <i class="fas fa-times-circle fa-lg"></i>
                </div>
                <div>
                    <h3 class="fw-extrabold mb-0 font-monospace">{{ $inactiveFaculties }}</h3>
                    <p class="text-secondary small fw-bold mb-0">{{ __('app.inactive_faculties') }}</p>
                </div>
            </div>
            <div style="height:3px;background:#ef4444"></div>
        </div>
    </div>

    {{-- Faculties Table --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 d-flex align-items-center gap-2 text-dark">
                    <i class="fas fa-table text-primary"></i>
                    {{ __('app.all_faculties_list') }}
                </h5>
                <span class="badge bg-primary rounded-pill px-3">{{ $faculties->total() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 fw-bold text-secondary small text-uppercase">#</th>
                                <th class="py-3 fw-bold text-secondary small text-uppercase">{{ __('app.faculty_name_ar') }}</th>
                                <th class="py-3 fw-bold text-secondary small text-uppercase">{{ __('app.faculty_name_en') }}</th>
                                <th class="py-3 fw-bold text-secondary small text-uppercase">{{ __('app.description') }}</th>
                                <th class="py-3 fw-bold text-secondary small text-uppercase">{{ __('app.status') }}</th>
                                <th class="py-3 fw-bold text-secondary small text-uppercase">{{ __('app.majors_count') }}</th>
                                <th class="py-3 fw-bold text-secondary small text-uppercase text-center">{{ __('app.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faculties as $faculty)
                            <tr>
                                <td class="px-4 text-muted small font-monospace">{{ $faculty->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="faculty-avatar">
                                            <i class="fas fa-university" style="font-size: 0.95rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $faculty->name_ar }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-secondary small">{{ $faculty->name_en ?: '-' }}</td>
                                <td class="text-secondary small" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $faculty->description ?: '-' }}
                                </td>
                                <td>
                                    <span class="status-badge {{ $faculty->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                        <i class="fas fa-circle me-1" style="font-size:0.55rem"></i>
                                        {{ $faculty->status === 'active' ? __('app.active') : __('app.inactive') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary rounded-pill font-monospace px-3">
                                        {{ $faculty->majors_count }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        {{-- Edit --}}
                                        <a href="{{ route('admin.faculties.edit', $faculty) }}"
                                           class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                           data-bs-toggle="tooltip" title="{{ __('app.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Toggle Status --}}
                                        <form action="{{ route('admin.faculties.toggle-status', $faculty) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm {{ $faculty->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }} rounded-pill px-3"
                                                data-bs-toggle="tooltip"
                                                title="{{ $faculty->status === 'active' ? __('app.deactivate') : __('app.activate') }}">
                                                <i class="fas {{ $faculty->status === 'active' ? 'fa-ban' : 'fa-check' }}"></i>
                                            </button>
                                        </form>

                                        {{-- Delete --}}
                                        <form action="{{ route('admin.faculties.destroy', $faculty) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('{{ __('app.faculty_delete_confirm') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                data-bs-toggle="tooltip" title="{{ __('app.delete') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-5 text-center text-muted">
                                    <i class="fas fa-university fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">{{ app()->getLocale() == 'ar' ? 'لا توجد كليات مضافة حالياً.' : 'No faculties found.' }}</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($faculties->hasPages())
            <div class="card-footer bg-white border-0 p-3">
                {{ $faculties->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
