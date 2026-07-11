@extends('layouts.app')

@section('title', __('app.edit_admin') . ': ' . $admin->name)

@section('styles')
<style>
    .form-card { background: white; border-radius: 1rem; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
    .form-label { font-weight: 700; font-size: 0.85rem; color: #374151; }
    .form-control, .form-select {
        border-radius: 0.6rem;
        border: 1.5px solid #e5e7eb;
        padding: 0.65rem 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #134074;
        box-shadow: 0 0 0 3px rgba(19,64,116,0.08);
    }
    .section-divider { border-top: 2px solid #f1f5f9; margin: 1.8rem 0; }
    .admin-meta-badge { background:#f1f5f9; border-radius:0.6rem; padding:0.9rem 1.2rem; }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none fw-bold"><i class="fas fa-home me-1"></i>{{ __('app.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}" class="text-decoration-none fw-bold">{{ __('app.admin_management') }}</a></li>
                <li class="breadcrumb-item active">{{ __('app.edit_admin') }}</li>
            </ol>
        </nav>

        <div class="form-card p-4 p-md-5">
            {{-- Header --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-4" style="width:52px;height:52px;">
                    <i class="fas fa-user-edit fa-lg"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark">{{ __('app.edit_admin') }}: {{ $admin->name }}</h4>
                    <p class="text-muted small mb-0">{{ __('app.edit_admin_hint') }}</p>
                </div>
            </div>

            {{-- Current Admin Meta --}}
            <div class="admin-meta-badge mb-4 d-flex flex-wrap gap-3 align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-calendar-alt text-muted small"></i>
                    <span class="text-muted small">{{ __('app.created_at') }}: <strong>{{ $admin->created_at->format('Y-m-d') }}</strong></span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-envelope text-muted small"></i>
                    <span class="text-muted small">{{ $admin->email }}</span>
                </div>
                @if($admin->id === auth()->id())
                    <span class="badge bg-warning text-dark">{{ __('app.you') }}</span>
                @endif
            </div>

            <div class="section-divider"></div>

            <form action="{{ route('admin.admins.update', $admin) }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div class="mb-4">
                    <label for="name" class="form-label">
                        <i class="fas fa-user me-1 text-primary"></i> {{ __('app.full_name') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $admin->name) }}"
                        placeholder="{{ __('app.full_name_placeholder') }}"
                        autocomplete="name" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope me-1 text-primary"></i> {{ __('app.email') }} <span class="text-danger">*</span>
                    </label>
                    <input type="email" name="email" id="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $admin->email) }}"
                        autocomplete="email" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="section-divider"></div>

                {{-- Password section (optional) --}}
                <p class="fw-bold text-secondary small mb-3 text-uppercase">
                    <i class="fas fa-key me-1"></i> {{ __('app.change_password_optional') }}
                </p>

                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-1 text-primary"></i> {{ __('app.new_password') }}
                    </label>
                    <input type="password" name="password" id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="{{ __('app.leave_blank_no_change') }}"
                        autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted small mt-1"><i class="fas fa-info-circle me-1"></i>{{ __('app.password_min_hint') }}</div>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-lock me-1 text-primary"></i> {{ __('app.confirm_new_password') }}
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="form-control"
                        placeholder="{{ __('app.leave_blank_no_change') }}"
                        autocomplete="new-password">
                </div>

                <div class="section-divider"></div>

                <div class="row g-4">
                    {{-- Signer Role --}}
                    <div class="col-md-6">
                        <label for="signer_role" class="form-label">
                            <i class="fas fa-file-signature me-1 text-primary"></i> المنصب التوقيعي
                        </label>
                        <select name="signer_role" id="signer_role" class="form-select">
                            <option value="">-- بدون منصب توقيعي --</option>
                            <option value="عميد الكلية" {{ old('signer_role', $admin->signer_role) == 'عميد الكلية' ? 'selected' : '' }}>عميد الكلية</option>
                            <option value="مسجل الكلية" {{ old('signer_role', $admin->signer_role) == 'مسجل الكلية' ? 'selected' : '' }}>مسجل الكلية</option>
                            <option value="مدير إدارة شؤون الخريجين" {{ old('signer_role', $admin->signer_role) == 'مدير إدارة شؤون الخريجين' ? 'selected' : '' }}>مدير إدارة شؤون الخريجين</option>
                            <option value="المختص الأكاديمي" {{ old('signer_role', $admin->signer_role) == 'المختص الأكاديمي' ? 'selected' : '' }}>المختص الأكاديمي</option>
                            <option value="المسجل العام" {{ old('signer_role', $admin->signer_role) == 'المسجل العام' ? 'selected' : '' }}>المسجل العام</option>
                            <option value="نائب رئيس الجامعة لشؤون الطلاب" {{ old('signer_role', $admin->signer_role) == 'نائب رئيس الجامعة لشؤون الطلاب' ? 'selected' : '' }}>نائب رئيس الجامعة لشؤون الطلاب</option>
                        </select>
                    </div>

                    {{-- Role --}}
                    <div class="col-md-6">
                        <label for="role" class="form-label">
                            <i class="fas fa-id-badge me-1 text-primary"></i> {{ __('app.role') }} <span class="text-danger">*</span>
                        </label>
                        <select name="role" id="role"
                            class="form-select @error('role') is-invalid @enderror" required>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role', $admin->role) == $role ? 'selected' : '' }}>
                                    {{ __('app.roles.' . $role) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6">
                        <label for="is_active" class="form-label">
                            <i class="fas fa-toggle-on me-1 text-primary"></i> {{ __('app.account_status') }} <span class="text-danger">*</span>
                        </label>
                        <select name="is_active" id="is_active"
                            class="form-select @error('is_active') is-invalid @enderror" required
                            {{ $admin->id === auth()->id() ? 'disabled' : '' }}>
                            <option value="1" {{ old('is_active', $admin->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>{{ __('app.active') }}</option>
                            <option value="0" {{ old('is_active', $admin->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>{{ __('app.inactive') }}</option>
                        </select>
                        {{-- If disabled, still submit the value --}}
                        @if($admin->id === auth()->id())
                            <input type="hidden" name="is_active" value="{{ $admin->is_active ? '1' : '0' }}">
                        @endif
                        @if($admin->id === auth()->id())
                            <div class="form-text text-warning small mt-1"><i class="fas fa-exclamation-triangle me-1"></i>{{ __('app.cannot_deactivate_yourself') }}</div>
                        @endif
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="section-divider"></div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">
                        <i class="fas fa-arrow-right me-1"></i> {{ __('app.cancel') }}
                    </a>
                    <button type="submit" class="btn btn-warning rounded-pill px-5 fw-bold shadow-sm text-dark">
                        <i class="fas fa-save me-2"></i> {{ __('app.save_changes') }}
                    </button>
                </div>
            </form>

            {{-- Signature Upload Section (shown only for signatory admins) --}}
            @if($admin->signer_role)
            <div class="section-divider"></div>
            <div class="mt-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-file-signature me-2 text-primary"></i>التوقيع الإلكتروني
                </h5>
                <p class="text-muted small mb-3">ارسم توقيعك مباشرة بالماوس أو الإصبع (للموبايل). سيظهر توقيعك على الوثائق الرسمية عند التوقيع.</p>

                @if($admin->signature_image)
                    <div class="mb-3 p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                        <img src="{{ $admin->signatureUrl() }}" alt="توقيعي" style="max-height: 40px; max-width: 120px; border: 1px solid #ddd; padding: 4px; background: white; border-radius: 6px;">
                        <div>
                            <span class="text-success small fw-bold"><i class="fas fa-check-circle me-1"></i>التوقيع الحالي</span>
                        </div>
                    </div>
                @else
                    <div class="mb-3 p-3 bg-warning bg-opacity-10 rounded-3">
                        <span class="text-warning small fw-bold"><i class="fas fa-exclamation-circle me-1"></i>لم تقم بحفظ توقيعك بعد</span>
                    </div>
                @endif

                <div style="position: relative; width: 100%; max-width: 400px;">
                    <canvas id="sigCanvas" width="400" height="150"
                        style="border: 2px dashed #ccc; border-radius: 8px; background: #fff; cursor: crosshair; width: 100%; touch-action: none;">
                    </canvas>
                    <small class="text-muted d-block mt-1">ارسم توقيعك بالماوس أو الإصبع (للموبايل)</small>
                </div>
                <div class="d-flex gap-2 mt-2">
                    <button type="button" id="clearCanvas" class="btn btn-outline-secondary rounded-pill px-3">
                        <i class="fas fa-eraser me-1"></i> مسح
                    </button>
                    <button type="button" id="saveCanvas" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="fas fa-check me-1"></i> حفظ التوقيع
                    </button>
                </div>

                <form id="sigForm" action="{{ route('admin.profile.signature.upload') }}" method="POST" style="display:none;">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $admin->id }}">
                    <input type="hidden" name="signature_data" id="signatureData">
                </form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('sigCanvas');
    const ctx = canvas.getContext('2d');
    let drawing = false;

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return { x: (clientX - rect.left) * scaleX, y: (clientY - rect.top) * scaleY };
    }

    function startDraw(e) { e.preventDefault(); drawing = true; ctx.beginPath(); const p = getPos(e); ctx.moveTo(p.x, p.y); }
    function draw(e) { if (!drawing) return; e.preventDefault(); const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.lineWidth = 2; ctx.strokeStyle = '#000'; ctx.lineCap = 'round'; ctx.stroke(); }
    function stopDraw() { drawing = false; ctx.closePath(); }

    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDraw);
    canvas.addEventListener('mouseleave', stopDraw);
    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    canvas.addEventListener('touchend', stopDraw);

    document.getElementById('clearCanvas').addEventListener('click', function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    });

    document.getElementById('saveCanvas').addEventListener('click', function() {
        const data = canvas.toDataURL('image/png');
        document.getElementById('signatureData').value = data;
        document.getElementById('sigForm').submit();
    });
});
</script>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
