@extends('layouts.app')

@section('title', __('app.documents_my_requests_title'))

@endsection

@section('content')
<div class="container py-4">
    
    <!-- Header Section -->
    <div class="dashboard-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-4 position-relative" style="z-index: 1;">
            <div class="bg-white bg-opacity-10 p-3 rounded-circle text-white d-none d-md-block">
                <i class="fas fa-file-signature fa-2x"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-1">{{ __('app.documents_my_requests_title') }}</h2>
                <p class="mb-0 text-white-50 fs-5">{{ __('app.documents_my_requests_intro') }}</p>
            </div>
        </div>
        <div style="z-index: 1;">
            <a href="{{ route('graduate.documents.create') }}" class="btn btn-light text-primary btn-lg rounded-pill fw-bold px-4 shadow-sm">
                <i class="fas fa-plus-circle me-2"></i>{{ __('app.request_document') }}
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-3 rounded-4 shadow-sm mb-4 d-flex flex-wrap gap-2 align-items-center">
        <span class="text-muted fw-bold ms-3"><i class="fas fa-filter me-2"></i> تصفية:</span>
        <a href="{{ route('graduate.documents.index') }}" 
           class="btn filter-btn {{ ($filter ?? 'all') === 'all' ? 'btn-primary' : 'btn-light text-secondary' }}">
           {{ __('app.documents_filter_all') }}
        </a>
        <a href="{{ route('graduate.documents.index', ['filter' => 'in_progress']) }}" 
           class="btn filter-btn {{ ($filter ?? '') === 'in_progress' ? 'btn-primary' : 'btn-light text-secondary' }}">
           {{ __('app.documents_filter_in_progress') }}
        </a>
        <a href="{{ route('graduate.documents.index', ['filter' => 'ready']) }}" 
           class="btn filter-btn {{ ($filter ?? '') === 'ready' ? 'btn-primary' : 'btn-light text-secondary' }}">
           {{ __('app.documents_filter_ready') }}
        </a>
        <a href="{{ route('graduate.documents.index', ['filter' => 'rejected']) }}" 
           class="btn filter-btn {{ ($filter ?? '') === 'rejected' ? 'btn-primary' : 'btn-light text-secondary' }}">
           {{ __('app.documents_filter_rejected') }}
        </a>
    </div>

    <!-- Requests Grid -->
    <div class="row g-4">
        @forelse($requests as $req)
            @php
                $pdfOk = $req->issuedDocument && \Illuminate\Support\Facades\Storage::disk('public')->exists($req->issuedDocument->pdf_path);
                $canDownload = in_array($req->status, ['READY', 'ISSUED'], true) && $pdfOk;
                $docName = app()->getLocale() === 'ar' ? $req->documentType->name_ar : $req->documentType->name_en;
            @endphp
            
            <div class="col-md-6 col-lg-4">
                <div class="request-card h-100 d-flex flex-column p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="tracking-badge">{{ $req->tracking_code }}</div>
                        <x-status-badge :status="$req->status" />
                    </div>
                    
                    <div class="d-flex align-items-center gap-3 border-bottom pb-3 mb-3">
                        <div class="doc-icon bg-light text-primary">
                            <i class="{{ $req->documentType->code === 'ACADEMIC_RECORD' ? 'fas fa-graduation-cap' : 'fas fa-award' }}"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">{{ $docName }}</h5>
                            <div class="small text-muted"><i class="far fa-calendar-alt me-1"></i> {{ $req->created_at->format('Y-m-d') }}</div>
                        </div>
                    </div>
                    
                    <div class="mt-auto d-flex gap-2">
                        <a href="{{ route('graduate.documents.show', $req) }}" class="btn btn-light text-primary fw-bold flex-grow-1 rounded-pill">
                            التفاصيل <i class="fas fa-arrow-left ms-1"></i>
                        </a>
                        @if($canDownload)
                            <a href="{{ route('graduate.documents.download', $req) }}" class="btn btn-success fw-bold flex-grow-1 rounded-pill" title="{{ __('app.documents_download') }}">
                                تحميل <i class="fas fa-download ms-1"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <x-empty-state
                    icon="fa-folder-open"
                    :title="__('app.documents_empty_list')"
                    message="{{ __('app.documents_empty_hint') }}"
                    :action="route('graduate.documents.create')"
                    :actionLabel="__('app.request_document')"
                />
            </div>
        @endforelse
    </div>

    @if($requests->hasPages())
        <div class="d-flex justify-content-center mt-5">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
