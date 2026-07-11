@extends('layouts.app')

@section('title', __('app.partner_employers'))

@section('content')
<div class="mb-4">
    <x-page-header 
        :title="__('app.partner_employers')"
        subtitle="{{ app()->getLocale() === 'ar' ? 'تصفح جهات العمل والشركات الشريكة المعتمدة في النظام.' : 'Browse approved partner employers and companies.' }}"
        icon="fa-building"
    />
</div>

<!-- Search Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('graduate.employers.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-9 col-sm-8">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث باسم الشركة، القطاع، أو المدينة...' : 'Search by company name, sector, or city...' }}" 
                           value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-3 col-sm-4">
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">
                    {{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Listing -->
<div class="row">
    @forelse($employers as $employer)
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-start mb-3">
                        <div class="flex-shrink-0">
                            @if($employer->logo)
                                <img src="{{ asset('storage/' . $employer->logo) }}" alt="Logo" class="rounded border p-1" style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <div class="bg-light text-primary rounded border p-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="fas fa-building fa-2x"></i>
                                </div>
                            @endif
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <h5 class="fw-bold mb-1 text-dark">{{ $employer->company_name }}</h5>
                            <div class="d-flex flex-wrap gap-2 text-muted small mt-1">
                                <span><i class="fas fa-tag me-1"></i> {{ $employer->industry ?: __('app.not_available') }}</span>
                                @if($employer->address)
                                    <span><i class="fas fa-map-marker-alt me-1"></i> {{ $employer->address }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <p class="text-muted small flex-grow-1 mb-4">
                        {{ Str::limit($employer->description, 150) ?: (app()->getLocale() === 'ar' ? 'لا يوجد وصف متاح.' : 'No description available.') }}
                    </p>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                            <i class="fas fa-briefcase me-1"></i> {{ $employer->jobs_count }} {{ app()->getLocale() === 'ar' ? 'وظائف نشطة' : 'Active Jobs' }}
                        </span>
                        
                        <a href="{{ route('graduate.employers.show', $employer->user_id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">
                            {{ app()->getLocale() === 'ar' ? 'عرض التفاصيل' : 'View Details' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <x-empty-state
                icon="fa-building"
                :title="__('app.no_employers_found')"
                :message="app()->getLocale() === 'ar' ? 'لم يتم العثور على جهات عمل مطابقة لبحثك في النظام.' : 'No partner employers matched your search criteria.'"
            />
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $employers->links() }}
</div>
@endsection
