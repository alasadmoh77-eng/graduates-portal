@extends('layouts.app')

@section('title', __('app.post_new_job'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h4 class="fw-bold mb-0">{{ __('app.post_new_job') }}</h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('employer.jobs.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('app.job_title') }}</label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Senior PHP Developer">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('app.job_type') }}</label>
                            <select name="job_type" class="form-select" required>
                                <option value="Full-time">{{ __('app.full_time') }}</option>
                                <option value="Part-time">{{ __('app.part_time') }}</option>
                                <option value="Remote">{{ __('app.remote') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('app.deadline') }}</label>
                            <input type="text" name="deadline" class="form-control date-picker-input" required dir="ltr" lang="en" readonly autocomplete="off" placeholder="YYYY-MM-DD">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('app.location') }}</label>
                        <input type="text" name="location" class="form-control" required placeholder="e.g. Marib, Yemen">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('app.description') }}</label>
                        <textarea name="description" class="form-control" rows="5" required placeholder="Detailed job description..."></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('app.requirements') }}</label>
                        <textarea name="requirements" class="form-control" rows="3" placeholder="Key skills and qualifications..."></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('employer.jobs.index') }}" class="btn btn-light px-4">{{ __('app.cancel') }}</a>
                        <button type="submit" class="btn btn-primary px-5">{{ __('app.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
