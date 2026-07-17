@extends('layouts.app')

@section('title', __('app.admin_management'))

@section('styles')
<style>
    .role-badge { font-size: 0.72rem; padding: 0.3rem 0.7rem; border-radius: 50px; font-weight: 700; letter-spacing: 0.5px; }
    .role-admin        { background: #e0e7ff; color: #3730a3; }
    .role-super_admin  { background: #fef3c7; color: #92400e; }
    .role-academic_admin { background: #dcfce7; color: #166534; }
    .role-finance_admin  { background: #fce7f3; color: #9d174d; }
    .status-active   { background: #dcfce7; color: #166534; }
    .status-inactive { background: #fee2e2; color: #991b1b; }
    .table-hover tbody tr:hover { background-color: #f8fafc; }
    .admin-avatar {
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
                    <i class="fas fa-user-shield fa-2x"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">{{ __('app.admin_management') }}</h2>
                    <p class="mb-0 text-white-50 fs-6">{{ __('app.admin_management_subtitle') }}</p>
                </div>
            </div>
            <div class="z-1">
                <a href="{{ route('admin.admins.create') }}" class="btn btn-gradient rounded-pill px-4 py-2 fw-bold text-white shadow">
                    <i class="fas fa-plus me-2"></i>{{ __('app.add_new_admin') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Summary KPI Cards --}}
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-4" style="width:50px;height:50px;">
                    <i class="fas fa-users-cog fa-lg"></i>
                </div>
                <div>
                    <h3 class="fw-extrabold mb-0 font-monospace"><span dir="ltr">{{ $totalAdmins }}</span></h3>
                    <p class="text-secondary small fw-bold mb-0">{{ __('app.total_admins') }}</p>
                </div>
            </div>
            <div style="height:3px;background:var(--primary-blue)"></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="icon-box bg-success bg-opacity-10 text-success rounded-4" style="width:50px;height:50px;">
                    <i class="fas fa-user-check fa-lg"></i>
                </div>
                <div>
                    <h3 class="fw-extrabold mb-0 font-monospace"><span dir="ltr">{{ $activeAdmins }}</span></h3>
                    <p class="text-secondary small fw-bold mb-0">{{ __('app.active_admins') }}</p>
                </div>
            </div>
            <div style="height:3px;background:#10b981"></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-4" style="width:50px;height:50px;">
                    <i class="fas fa-user-times fa-lg"></i>
                </div>
                <div>
                    <h3 class="fw-extrabold mb-0 font-monospace"><span dir="ltr">{{ $inactiveAdmins }}</span></h3>
                    <p class="text-secondary small fw-bold mb-0">{{ __('app.inactive_admins') }}</p>
                </div>
            </div>
            <div style="height:3px;background:#ef4444"></div>
        </div>
    </div>

    {{-- Admins Table --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 d-flex align-items-center gap-2 text-dark">
                    <i class="fas fa-table text-primary"></i>
                    {{ __('app.all_admins_list') }}
                </h5>
                <span class="badge bg-primary rounded-pill px-3"><span dir="ltr">{{ $totalAdmins }}</span></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="admins-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 fw-bold text-secondary small text-uppercase">الرقم</th>
                                <th class="py-3 fw-bold text-secondary small text-uppercase">{{ __('app.name') }}</th>
                                <th class="py-3 fw-bold text-secondary small text-uppercase">{{ __('app.email') }}</th>
                                <th class="py-3 fw-bold text-secondary small text-uppercase">{{ __('app.role') }}</th>
                                <th class="py-3 fw-bold text-secondary small text-uppercase">{{ __('app.status') }}</th>
                                <th class="py-3 fw-bold text-secondary small text-uppercase">{{ __('app.created_at') }}</th>
                                <th class="py-3 fw-bold text-secondary small text-uppercase text-center">{{ __('app.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admins as $admin)
                            <tr class="{{ $admin->id === auth()->id() ? 'table-primary' : '' }}">
                                <td class="px-4 text-muted small font-monospace" dir="ltr">{{ ($admins->currentPage() - 1) * $admins->perPage() + $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="admin-avatar">{{ mb_substr($admin->name, 0, 1) }}</div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $admin->name }}</div>
                                            @if($admin->id === auth()->id())
                                                <span class="badge bg-warning text-dark" style="font-size:0.65rem;">{{ __('app.you') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-secondary small">{{ $admin->email }}</td>
                                <td>
                                    <span class="role-badge role-{{ $admin->role }}">
                                        {{ __('app.roles.' . $admin->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="role-badge {{ $admin->is_active ? 'status-active' : 'status-inactive' }}">
                                        <i class="fas fa-circle me-1" style="font-size:0.55rem"></i>
                                        {{ $admin->is_active ? __('app.active') : __('app.inactive') }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $admin->created_at->format('Y-m-d') }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        {{-- Edit --}}
                                        <a href="{{ route('admin.admins.edit', $admin) }}"
                                           class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                           data-bs-toggle="tooltip" title="{{ __('app.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Toggle Status --}}
                                        <form action="{{ route('admin.admins.toggleStatus', $admin) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-sm {{ $admin->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} rounded-pill px-3"
                                                data-bs-toggle="tooltip"
                                                title="{{ $admin->is_active ? __('app.deactivate') : __('app.activate') }}">
                                                <i class="fas {{ $admin->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                            </button>
                                        </form>

                                        {{-- Delete --}}
                                        @if($admin->id !== auth()->id())
                                        <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('{{ __('app.admin_delete_confirm') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                data-bs-toggle="tooltip" title="{{ __('app.delete') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-5 text-center text-muted">
                                    <i class="fas fa-user-shield fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">{{ __('app.no_admins_found') }}</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($admins->hasPages())
            <div class="card-footer bg-white border-0 p-3">
                {{ $admins->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
