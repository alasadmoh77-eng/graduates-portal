@extends('layouts.app')

@section('title', __('app.academic_record_entry_title'))

@section('styles')
    <!-- تمت إزالة خطوط جوجل للعمل دون إنترنت -->
    <script defer src="{{ asset('assets/js/alpine.min.js') }}"></script>
    <script src="{{ asset('assets/js/lucide.min.js') }}"></script>
    <style>
        /* ============================================================
           ACADEMIC RECORD — Modern Professional Redesign
           ============================================================ */

        /* --- Design Tokens --- */
        :root {
            --ar-primary: #0b2545;
            --ar-primary-light: #134074;
            --ar-accent: #b89047;
            --ar-accent-hover: #a37c36;
            --ar-bg: #f4f6f9;
            --ar-card-bg: #ffffff;
            --ar-border: #d1d9e6;
            --ar-border-dark: #0f172a;
            --ar-text: #1d2d44;
            --ar-text-muted: #64748b;
            --ar-success: #10b981;
            --ar-danger: #ef4444;
            --ar-info-bg: #f0f4ff;
            --ar-radius: 12px;
            --ar-radius-sm: 8px;
            --ar-shadow: 0 4px 20px rgba(11,37,69,0.06);
            --ar-shadow-hover: 0 8px 30px rgba(11,37,69,0.1);
            --ar-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --ar-transcript-max: 210mm;
            --ar-level-gap: 1rem;
        }

        /* --- Page Root --- */
        .academic-toolbar-page {
            font-family: 'Tajawal', 'Cairo', sans-serif;
            color: var(--ar-text);
            padding: 1rem;
        }

        /* --- Breadcrumb & Page Header --- */
        .ar-page-header {
            background: var(--ar-card-bg);
            border-radius: var(--ar-radius);
            box-shadow: var(--ar-shadow);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.25rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            border-right: 4px solid var(--ar-accent);
        }
        .ar-page-header .breadcrumb {
            margin-bottom: 0.35rem;
            font-size: 0.82rem;
        }
        .ar-page-header .breadcrumb a {
            color: var(--ar-primary-light);
            text-decoration: none;
            font-weight: 600;
        }
        .ar-page-header .breadcrumb a:hover { color: var(--ar-accent); }
        .ar-page-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--ar-primary);
            margin: 0;
        }
        .ar-page-subtitle {
            font-size: 0.85rem;
            color: var(--ar-text-muted);
            margin-top: 0.25rem;
        }
        .ar-btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 1.35rem;
            border: 2px solid var(--ar-border);
            border-radius: 50px;
            background: var(--ar-card-bg);
            color: var(--ar-text);
            font-weight: 700;
            font-size: 0.85rem;
            text-decoration: none;
            transition: var(--ar-transition);
        }
        .ar-btn-back:hover {
            border-color: var(--ar-primary);
            color: var(--ar-primary);
            box-shadow: var(--ar-shadow-hover);
        }

        /* --- Transcript Layout --- */
        .academic-layout-root {
            width: 100%;
            max-width: min(var(--ar-transcript-max), 100%);
            margin-inline: auto;
            padding-bottom: 5.5rem;
        }

        .academic-page {
            width: 100%;
            max-width: var(--ar-transcript-max);
            min-height: 297mm;
            margin: 0 auto;
            padding: 6mm 7mm 8mm;
            background: white;
            box-shadow: 0 2px 24px rgba(0,0,0,0.07);
            border-radius: 6px;
            box-sizing: border-box;
        }

        /* --- Official Header (3-column bilingual) --- */
        .official-header {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            border: 1.5px solid var(--ar-border-dark);
            padding: 6px 12px;
            margin-bottom: -1px;
            background: linear-gradient(180deg, #fafbfd 0%, #f0f2f5 100%);
        }
        .header-left {
            text-align: right;
            font-size: 0.65rem;
            font-weight: 700;
            line-height: 1.35;
            color: var(--ar-primary);
        }
        .header-center { text-align: center; }
        .header-right {
            text-align: left;
            font-size: 0.65rem;
            font-weight: 700;
            font-family: 'Amiri', serif;
            line-height: 1.35;
            color: var(--ar-primary);
        }
        .uni-logo-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border: 2px solid var(--ar-primary);
            border-radius: 50%;
            margin-bottom: 4px;
            background: white;
        }
        .doc-title {
            font-size: 0.82rem;
            font-weight: 800;
            border: 1.5px solid var(--ar-border-dark);
            padding: 3px 18px;
            background: linear-gradient(135deg, #eef1f5, #e4e8ee);
            margin: 0;
            display: inline-block;
            letter-spacing: 0.3px;
        }

        /* --- Student Info Box --- */
        .student-info-box {
            border: 1.5px solid var(--ar-border-dark);
            padding: 0.5rem 0.65rem;
            font-size: 0.72rem;
            margin-bottom: 0.5rem;
            background: #fafbfd;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 3px;
            flex-wrap: wrap;
        }
        .info-item {
            display: flex;
            gap: 4px;
            flex: 1;
            border-bottom: 1px dotted #c0c8d4;
            min-width: 120px;
            padding-bottom: 2px;
        }
        .info-label {
            font-weight: 800;
            white-space: nowrap;
            color: var(--ar-primary);
            font-size: 0.68rem;
        }
        .info-value {
            border: none;
            background: transparent;
            width: 100%;
            outline: none;
            font-size: 0.72rem;
            color: var(--ar-text);
            font-weight: 600;
        }
        .info-value:focus {
            background: #fffef2;
            border-radius: 2px;
        }

        /* --- Level Containers --- */
        .level-container {
            margin-bottom: var(--ar-level-gap);
            border: 1.5px solid var(--ar-border-dark);
            background: #fff;
            border-radius: 4px;
            overflow: hidden;
            transition: var(--ar-transition);
        }
        .level-header {
            background: linear-gradient(135deg, #0b2545 0%, #134074 100%);
            color: #fff;
            border-bottom: 1px solid var(--ar-border-dark);
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
            align-items: center;
            gap: 0.5rem 0.75rem;
            padding: 0.5rem 0.75rem;
            font-weight: 800;
            font-size: 0.72rem;
        }
        .level-header > span:first-child { text-align: right; }
        .level-header > span:nth-child(2) { text-align: center; white-space: nowrap; }
        .level-header > span:last-child { text-align: left; }
        .level-header input.level-year-input {
            width: 5.5rem;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 4px;
            background: rgba(255,255,255,0.15);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 800;
            text-align: center;
            padding: 3px 6px;
            transition: var(--ar-transition);
        }
        .level-header input.level-year-input::placeholder { color: rgba(255,255,255,0.5); }
        .level-header input.level-year-input:focus {
            background: rgba(255,255,255,0.25);
            border-color: rgba(255,255,255,0.6);
            outline: none;
        }

        /* --- Semester Grid --- */
        .semester-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            align-items: stretch;
            gap: 0;
            border-top: none;
        }
        .semester-box {
            border-inline-start: 1px solid var(--ar-border-dark);
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }
        .semester-box:first-child { border-inline-start: none; }
        .semester-title {
            background: linear-gradient(180deg, #f8f9fb 0%, #eef0f4 100%);
            text-align: center;
            font-weight: 800;
            font-size: 0.68rem;
            border-bottom: 1px solid var(--ar-border-dark);
            padding: 0.4rem 0.5rem;
            flex-shrink: 0;
            color: var(--ar-primary);
            letter-spacing: 0.2px;
        }
        .semester-box .academic-table { flex: 1; }

        /* --- Academic Table --- */
        .academic-table {
            width: 100%;
            table-layout: fixed;
            font-size: 0.65rem;
            border-collapse: collapse;
        }
        .academic-table col.colg-del { width: 32px; }
        .academic-table col.colg-idx { width: 28px; }
        .academic-table col.colg-subject { width: auto; }
        .academic-table col.colg-hours { width: 38px; }
        .academic-table col.colg-score { width: 42px; }
        .academic-table col.colg-rating { width: 56px; }
        .academic-table .col-subject-head { text-align: right; padding-inline: 6px 4px; }
        .academic-table th {
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
            background: linear-gradient(180deg, #f1f5f9, #e8ecf2);
            font-weight: 800;
            line-height: 1.2;
            color: var(--ar-primary);
            font-size: 0.63rem;
        }
        .academic-table td {
            border: 1px solid #000;
            padding: 0;
            min-height: 24px;
            vertical-align: middle;
            position: relative;
        }
        .academic-subject-cell { vertical-align: top !important; padding: 3px 4px !important; }
        .academic-empty-row td {
            padding: 0.5rem 0.4rem !important;
            text-align: center;
            color: var(--ar-text-muted);
            font-size: 0.62rem;
            font-style: italic;
            background: #fafafa;
        }
        .academic-table input {
            width: 100%;
            box-sizing: border-box;
            border: none;
            padding: 3px 4px;
            font-size: 0.68rem;
            text-align: center;
            outline: none;
            background: transparent;
            min-height: 24px;
            transition: background 0.2s;
        }
        .academic-table input:focus {
            background: #fffef2;
        }
        .academic-table select.subject-select {
            width: 100%; max-width: 100%; font-size: 0.62rem; padding: 2px 3px;
            border: none; background: #fafafa;
            text-align: right; direction: rtl; min-height: 22px;
            cursor: pointer;
        }
        .academic-table select.subject-select:focus {
            background: #f0f4ff;
        }
        .subject-editor-ui { text-align: right; }
        .subject-custom-name {
            width: 100%; border: none; padding: 2px 4px; font-size: 0.68rem;
            text-align: right; background: #fffef7;
        }
        .subject-custom-name:focus { background: #fff8e1; }
        .subject-print-only { display: none; font-size: 0.68rem; text-align: right; padding: 2px 4px; min-height: 18px; }
        .field-readonly, input.field-readonly {
            background: #f1f5f9 !important;
            color: #334155 !important;
            cursor: default;
        }
        .field-readonly-inline {
            display: inline-block;
            min-width: 2.5rem;
            font-weight: 800;
            color: #fff;
            background: rgba(255,255,255,0.15);
            padding: 1px 8px;
            border-radius: 4px;
        }
        .col-m { font-weight: 700; background: #f0f0f0; text-align: center; }
        .col-s, .col-d, .col-t { text-align: center; }
        .col-del { text-align: center; background: #fff5f5; }

        /* --- Row Actions --- */
        .btn-del-row {
            color: #ef4444;
            border: none;
            background: none;
            font-weight: bold;
            padding: 0;
            width: 100%;
            height: 100%;
            line-height: 1;
            font-size: 14px;
            display: block;
            cursor: pointer;
            transition: var(--ar-transition);
        }
        .btn-del-row:hover { background-color: #ef4444; color: white; }
        .add-btn-row { text-align: center; padding: 2px; }
        .btn-add-sub {
            font-size: 0.62rem;
            color: var(--ar-primary-light);
            font-weight: 800;
            cursor: pointer;
            border: 1.5px dashed var(--ar-primary-light);
            padding: 2px 14px;
            border-radius: 6px;
            background: transparent;
            transition: var(--ar-transition);
        }
        .btn-add-sub:hover {
            background: var(--ar-primary);
            color: #fff;
            border-color: var(--ar-primary);
        }

        /* --- Level Summary --- */
        .level-sum {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            align-items: center;
            gap: 0.5rem 0.75rem;
            border-top: 1.5px solid var(--ar-border-dark);
            padding: 0.55rem 0.75rem;
            font-size: 0.68rem;
            font-weight: 800;
            background: linear-gradient(180deg, #f8f9fb 0%, #f0f2f5 100%);
        }
        .level-sum-part {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            flex-wrap: wrap;
            min-width: 0;
        }
        .level-sum-part--points { justify-content: flex-end; text-align: right; }
        .level-sum-part--result { justify-content: flex-start; text-align: right; }
        .level-result-input {
            min-width: 6.5rem;
            flex: 1;
            max-width: 12rem;
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 6px;
            background: #fff !important;
            font-weight: 800;
            padding: 5px 10px !important;
            text-align: center;
            font-size: 0.68rem;
            transition: var(--ar-transition);
        }
        .level-result-input:focus {
            border-color: var(--ar-primary) !important;
            box-shadow: 0 0 0 3px rgba(11,37,69,0.08);
            outline: none;
        }

        /* --- Signatures --- */
        .signatures-row {
            display: flex;
            justify-content: space-around;
            margin-top: 8px;
            border-top: 1.5px solid #000;
            padding-top: 6px;
            text-align: center;
            flex-wrap: wrap;
        }
        .sig-item { flex: 1; min-width: 100px; }
        .sig-title { font-weight: 800; font-size: 0.72rem; margin-bottom: 22px; color: var(--ar-primary); }
        .sig-line { border-top: 1px dotted #000; width: 60%; margin: 0 auto; }

        /* --- Floating Admin Toolbar --- */
        .admin-toolbar {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 12px;
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 12px 28px;
            border-radius: 50px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18), 0 0 0 1px rgba(0,0,0,0.04);
            border: 1px solid rgba(255,255,255,0.6);
            z-index: 2000;
            transition: var(--ar-transition);
        }
        .btn-admin {
            border: none;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: var(--ar-transition);
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            position: relative;
            cursor: pointer;
        }
        .btn-admin::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%) scale(0.8);
            background: var(--ar-primary);
            color: #fff;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.68rem;
            font-weight: 700;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s, transform 0.2s;
        }
        .btn-admin:hover::after {
            opacity: 1;
            transform: translateX(-50%) scale(1);
        }
        .btn-print { background: linear-gradient(135deg, #134074, #0b2545); }
        .btn-save { background: linear-gradient(135deg, #10b981, #059669); }
        .btn-reset { background: linear-gradient(135deg, #94a3b8, #64748b); }
        .btn-admin:hover:not(:disabled) {
            transform: scale(1.12);
            box-shadow: 0 6px 18px rgba(0,0,0,0.25);
        }
        .btn-admin:active:not(:disabled) { transform: scale(0.96); }
        .btn-admin:disabled { opacity: 0.5; cursor: not-allowed; }

        /* ============================================================
           RESPONSIVE — Mobile
           ============================================================ */
        @media (max-width: 767.98px) {
            .academic-toolbar-page { padding: 0.5rem; }
            .ar-page-header {
                padding: 1rem;
                flex-direction: column;
                align-items: flex-start;
            }
            .ar-page-title { font-size: 1.1rem; }
            .ar-btn-back { width: 100%; justify-content: center; }
            .academic-page {
                padding: 3mm 3mm 5mm;
                min-height: auto;
                border-radius: 4px;
            }
            .official-header {
                grid-template-columns: 1fr;
                gap: 6px;
                padding: 8px;
                text-align: center;
            }
            .header-left, .header-right { text-align: center; font-size: 0.6rem; }
            .student-info-box { padding: 0.4rem; font-size: 0.66rem; }
            .info-row { flex-direction: column; gap: 4px; }
            .info-item { min-width: 100%; }
            .level-header {
                grid-template-columns: 1fr;
                gap: 4px;
                text-align: center !important;
                padding: 0.4rem 0.5rem;
                font-size: 0.66rem;
            }
            .level-header > span { text-align: center !important; }
            .semester-grid { grid-template-columns: 1fr; }
            .semester-box { border-inline-start: none; border-top: 1px solid var(--ar-border-dark); }
            .semester-box:first-child { border-top: none; }
            .academic-table { font-size: 0.58rem; }
            .academic-table th { padding: 3px 1px; font-size: 0.56rem; }
            .academic-table input { font-size: 0.6rem; min-height: 20px; padding: 2px; }
            .academic-table select.subject-select { font-size: 0.56rem; }
            .level-sum {
                grid-template-columns: 1fr;
                gap: 6px;
                padding: 0.4rem 0.5rem;
                font-size: 0.62rem;
            }
            .level-sum-part { justify-content: center !important; text-align: center !important; }
            .level-result-input { max-width: 100%; }
            .signatures-row { gap: 8px; }
            .sig-item { min-width: 80px; }
            .sig-title { font-size: 0.62rem; margin-bottom: 14px; }
            .admin-toolbar {
                padding: 8px 16px;
                gap: 8px;
                bottom: 12px;
                border-radius: 40px;
            }
            .btn-admin { width: 42px; height: 42px; }
        }

        /* ============================================================
           PRINT STYLES — Preserve A4 layout
           ============================================================ */
        @media print {
            @page { size: A4; margin: 3mm; }
            body { background: white !important; padding: 0; margin: 0; }
            .academic-toolbar-page { padding: 0; }
            .ar-page-header { display: none !important; }
            .academic-layout-root { max-width: none; padding-bottom: 0; }
            .academic-page {
                margin: 0; padding: 2mm 3mm;
                width: 100%; max-width: none;
                box-shadow: none; border: none; border-radius: 0;
            }
            .official-header { background: #fff !important; }
            .academic-table { table-layout: fixed; }
            .admin-toolbar, .col-del, .add-btn-row, .no-print { display: none !important; }
            .academic-empty-row { display: none !important; }
            .level-container { break-inside: avoid; border-radius: 0; }
            .level-header { background: #f1f5f9 !important; color: #000 !important; }
            .field-readonly-inline { color: #000 !important; background: transparent !important; }
            .official-header, .student-info-box, .level-container { margin-bottom: 1px !important; }
            .level-sum { background: #fff !important; padding: 0.25rem 0.4rem !important; }
            .signatures-row { margin-top: 2px !important; }
            input { border: none !important; background: transparent !important; color: #000; }
            .level-result-input { border: none !important; background: transparent !important; }
            .level-year-input { border: none !important; background: transparent !important; color: #000 !important; }
            .subject-editor-ui { display: none !important; }
            .subject-print-only { display: block !important; }
            select.subject-select { display: none !important; }
        }
    </style>
@endsection

@section('content')
@php
    $initial = $initialData ?? ['student' => [], 'levels' => []];
    $saveEndpoint = $saveUrl ?? '#';
    $catalog = $subjectCatalog ?? [];
@endphp
<div class="academic-toolbar-page">
    {{-- Page Header --}}
    <div class="ar-page-header no-print">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('app.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.graduates') }}">{{ __('app.manage_graduates') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('app.academic_record_entry_title') }}</li>
                </ol>
            </nav>
            <h2 class="ar-page-title">{{ __('app.academic_record_entry_title') }}</h2>
            @isset($graduate)
                <p class="ar-page-subtitle">
                    {{ __('app.graduate_name') }}: <strong>{{ $graduate->name }}</strong>
                    @if($graduate->graduate?->university_id)
                        — {{ __('app.university_id') }}: <strong>{{ $graduate->graduate->university_id }}</strong>
                    @endif
                </p>
            @endisset
        </div>
        <a href="{{ route('admin.reports.graduates') }}" class="ar-btn-back">
            <i class="fas fa-arrow-right"></i> {{ __('app.cancel') }}
        </a>
    </div>

    {{-- x-data must wrap the toolbar too; otherwise @click handlers on floating buttons are outside Alpine scope --}}
    <div class="academic-layout-root" x-data="documentState({{ \Illuminate\Support\Js::from($initial) }}, {{ \Illuminate\Support\Js::from($saveEndpoint) }}, {{ \Illuminate\Support\Js::from($catalog) }})">
    <div class="academic-page">
        <div class="official-header">
            <div class="header-left">
                الجمهـورية اليمنيـة<br>
                جامعـة إقليم سبأ<br>
                كلية تكنولوجيا المعلومات وعلوم الحاسوب<br>
                قسم : علوم الحاسوب
            </div>
            <div class="header-center">
                <div class="uni-logo-wrapper">
                    <i data-lucide="shield" size="20"></i>
                </div>
                <br>
                <h1 class="doc-title">سجل الطالب الأكاديمي للمراجعة</h1>
            </div>
            <div class="header-right">
                Republic of Yemen<br>
                Saba Region University<br>
                Faculty of IT &amp; Comp. Science<br>
                Dept: Computer Science
            </div>
        </div>

        <div class="student-info-box">
            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">الاسـم (عربي):</span>
                    <input type="text" class="info-value" x-model="student.name">
                </div>
                <div class="info-item">
                    <span class="info-label">الاسم (إنجليزي):</span>
                    <input type="text" class="info-value" dir="ltr" style="text-align: left;" x-model="student.name_en">
                </div>
                <div class="info-item" style="flex: 0 0 140px;">
                    <span class="info-label">الرقـم:</span>
                    <input type="text" class="info-value text-center" x-model="student.id">
                </div>
            </div>
            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">الدرجة (عربي):</span>
                    <input type="text" class="info-value" x-model="student.degree">
                </div>
                <div class="info-item">
                    <span class="info-label">الدرجة (إنجليزي):</span>
                    <input type="text" class="info-value" dir="ltr" style="text-align: left;" x-model="student.degree_en">
                </div>
                <div class="info-item">
                    <span class="info-label">مجموع النقاط (Σ درجة×ساعة):</span>
                    <input type="text" class="info-value text-center field-readonly" readonly tabindex="-1" x-model="student.total" title="يُحسب تلقائياً">
                </div>
                <div class="info-item">
                    <span class="info-label">المعدل %:</span>
                    <input type="text" class="info-value text-center field-readonly" readonly tabindex="-1" x-model="student.gpa" title="يُحسب تلقائياً">
                </div>
                <div class="info-item">
                    <span class="info-label">التقدير العام:</span>
                    <input type="text" class="info-value text-center field-readonly" readonly tabindex="-1" x-model="student.rating" title="من المعدل الكلي">
                </div>
                <div class="info-item">
                    <span class="info-label">دورا:</span>
                    <input type="text" class="info-value text-center" x-model="student.dora">
                </div>
            </div>
            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">مرتبة الشرف:</span>
                    <input type="text" class="info-value" x-model="student.honors">
                </div>
                <div class="info-item">
                    <span class="info-label">عام التخرج:</span>
                    <input type="text" class="info-value text-center" x-model="student.gradYear">
                </div>
                <div class="info-item">
                    <span class="info-label">عام الالتحاق:</span>
                    <input type="text" class="info-value text-center" x-model="student.enrollmentYear">
                </div>
            </div>
        </div>

        <template x-for="(level, lIdx) in levels" :key="lIdx">
            <div class="level-container">
                <div class="level-header">
                    <span>المستـوى <span x-text="level.name"></span></span>
                    <span>العام الجامعي: <input type="text" class="level-year-input" x-model="level.year"></span>
                    <span>المعدل %: <span class="field-readonly-inline" x-text="level.avg || '—'"></span></span>
                </div>
                <div class="semester-grid">
                    <template x-for="(sem, sIdx) in level.semesters" :key="sIdx">
                        <div class="semester-box">
                            <div class="semester-title" x-text="sIdx == 0 ? 'الفصـل الدراسي الأول' : 'الفصـل الدراسي الثاني'"></div>
                            <table class="academic-table">
                                <colgroup>
                                    <col class="colg-del">
                                    <col class="colg-idx">
                                    <col class="colg-subject">
                                    <col class="colg-hours">
                                    <col class="colg-score">
                                    <col class="colg-rating">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="col-del d-print-none">ح</th>
                                        <th class="col-m">م</th>
                                        <th class="col-subject-head">اســـــــــــــــــم الـمـادة</th>
                                        <th class="col-s">س.م</th>
                                        <th class="col-d">الدرجة</th>
                                        <th class="col-t">التقدير</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(sub, subIdx) in sem.subjects" :key="subIdx">
                                        <tr>
                                            <td class="col-del d-print-none">
                                                <button type="button" @click="removeSub(lIdx, sIdx, subIdx)" class="btn-del-row" title="حذف مادة">&times;</button>
                                            </td>
                                            <td class="col-m" x-text="subIdx + 1"></td>
                                            <td class="academic-subject-cell">
                                                <div class="subject-editor-ui">
                                                    <select class="subject-select"
                                                        :value="selectValue(sub)"
                                                        @change="pickCatalog(lIdx, sIdx, subIdx, $event)">
                                                        <option value="">— اختر المادة —</option>
                                                        <option value="__custom__">مادة أخرى (يدوي)</option>
                                                        <template x-for="c in availableCourses(lIdx, sIdx, subIdx)" :key="c.key">
                                                            <option :value="c.key" x-text="c.name_ar"></option>
                                                        </template>
                                                    </select>
                                                    <input type="text" class="subject-custom-name d-print-none" x-show="!sub.catalog_key" x-model="sub.name" placeholder="اسم المادة">
                                                </div>
                                                <div class="subject-print-only" x-text="sub.name || '—'"></div>
                                            </td>
                                            <td class="col-s">
                                                <input type="text" x-model="sub.hours" :readonly="!!sub.catalog_key" :class="sub.catalog_key ? 'field-readonly' : ''" :tabindex="sub.catalog_key ? -1 : 0">
                                            </td>
                                            <td class="col-d"><input type="text" x-model="sub.score"></td>
                                            <td class="col-t"><input type="text" class="field-readonly" readonly tabindex="-1" x-model="sub.rating"></td>
                                        </tr>
                                    </template>
                                    <template x-if="sem.subjects.length === 0">
                                        <tr class="academic-empty-row">
                                            <td colspan="6" class="academic-placeholder">لا توجد مقررات في هذا الفصل — استخدم «إضافة مادة»</td>
                                        </tr>
                                    </template>
                                    <tr class="add-btn-row d-print-none">
                                        <td colspan="6">
                                            <button type="button" @click="addSub(lIdx, sIdx)" class="btn-add-sub">+ إضافة مادة</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
                <div class="level-sum">
                    <div class="level-sum-part level-sum-part--points">
                        <span>مجموع النقاط (المستوى):</span>
                        <span class="field-readonly-inline" style="color: var(--ar-primary); background: rgba(11,37,69,0.06);" x-text="level.totalPoints || '—'"></span>
                    </div>
                    <div class="level-sum-part level-sum-part--result">
                        <span class="text-nowrap">النتيجة النهائية للمستوى:</span>
                        <input type="text" class="level-result-input" x-model="level.result" placeholder="—">
                    </div>
                </div>
            </div>
        </template>

        <div class="signatures-row">
            <div class="sig-item"><div class="sig-title">المختص</div><div class="sig-line"></div></div>
            <div class="sig-item"><div class="sig-title">مدير إدارة الخريجين</div><div class="sig-line"></div></div>
            <div class="sig-item"><div class="sig-title">مسجل الكلية</div><div class="sig-line"></div></div>
            <div class="sig-item"><div class="sig-title">عميد الكلية</div><div class="sig-line"></div></div>
        </div>
    </div>

    <div class="admin-toolbar d-print-none">
        <button type="button" @click.prevent="resetData()" class="btn-admin btn-reset" data-tooltip="إعادة تعيين" title="إعادة تعيين"><i data-lucide="rotate-ccw"></i></button>
        <button type="button" @click.prevent="window.print()" class="btn-admin btn-print" data-tooltip="طباعة" title="طباعة"><i data-lucide="printer"></i></button>
        <button type="button" @click.prevent="saveData()" class="btn-admin btn-save" :disabled="saving" data-tooltip="حفظ" title="حفظ في النظام"><i data-lucide="save"></i></button>
    </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    /**
     * Weighted points = Σ (score × credit_hours); average % = weighted / Σ hours.
     * Per-subject rating from numeric score: 90+ ممتاز, 80+ جيد جداً, 70+ جيد, 60+ مقبول, else راسب.
     */
    function documentState(initial, saveUrl, catalog) {
        const initialClone = JSON.parse(JSON.stringify(initial));
        return {
            student: initialClone.student,
            levels: initialClone.levels,
            catalog: catalog && typeof catalog === 'object' ? catalog : {},
            saving: false,
            saveUrl,

            init() {
                this.normalizeLevels();
                this.$watch('levels', () => this.recalculateAll(), { deep: true });
                this.recalculateAll();
            },

            normalizeLevels() {
                this.levels.forEach((level, lIdx) => {
                    level.semesters.forEach((sem, sIdx) => {
                        sem.subjects.forEach((sub) => {
                            if (sub.catalog_key === undefined || sub.catalog_key === null) {
                                sub.catalog_key = '';
                            }
                            if (sub.catalog_key && !this.findCourse(lIdx + 1, sIdx + 1, sub.catalog_key)) {
                                sub.catalog_key = '';
                            }
                        });
                    });
                });
            },

            parseNum(v) {
                if (v === undefined || v === null) return NaN;
                const s = String(v).trim().replace(',', '.');
                if (s === '') return NaN;
                const n = parseFloat(s);
                return Number.isFinite(n) ? n : NaN;
            },

            findCourse(level1, sem1, key) {
                const list = this.catalog[level1]?.[sem1] || [];
                return list.find((c) => c.key === key) || null;
            },

            availableCourses(lIdx, sIdx, subIdx) {
                const L = lIdx + 1;
                const S = sIdx + 1;
                const list = this.catalog[L]?.[S] || [];
                const subs = this.levels[lIdx].semesters[sIdx].subjects;
                const taken = new Set();
                subs.forEach((row, i) => {
                    if (i !== subIdx && row.catalog_key) taken.add(row.catalog_key);
                });
                return list.filter((c) => !taken.has(c.key) || c.key === subs[subIdx].catalog_key);
            },

            selectValue(sub) {
                if (sub.catalog_key) return sub.catalog_key;
                if ((sub.name || '').trim() !== '') return '__custom__';
                return '';
            },

            pickCatalog(lIdx, sIdx, subIdx, ev) {
                const v = ev.target.value;
                const sub = this.levels[lIdx].semesters[sIdx].subjects[subIdx];
                if (v === '') {
                    sub.catalog_key = '';
                    sub.name = '';
                    sub.hours = '';
                } else if (v === '__custom__') {
                    sub.catalog_key = '';
                } else {
                    const c = this.findCourse(lIdx + 1, sIdx + 1, v);
                    if (c) {
                        sub.catalog_key = v;
                        sub.name = c.name_ar;
                        sub.hours = String(c.credit_hours);
                    }
                }
                this.recalculateAll();
            },

            ratingFromScore(sub) {
                const raw = String(sub.score ?? '').trim();
                if (raw === '') {
                    sub.rating = '';
                    return;
                }
                const s = this.parseNum(sub.score);
                if (!Number.isFinite(s)) {
                    sub.rating = '';
                    return;
                }
                if (s >= 90) sub.rating = 'ممتاز';
                else if (s >= 80) sub.rating = 'جيد جداً';
                else if (s >= 70) sub.rating = 'جيد';
                else if (s >= 60) sub.rating = 'مقبول';
                else sub.rating = 'راسب';
            },

            ratingFromAverage(avg) {
                if (!Number.isFinite(avg)) return '';
                if (avg >= 90) return 'ممتاز';
                if (avg >= 80) return 'جيد جداً';
                if (avg >= 70) return 'جيد';
                if (avg >= 60) return 'مقبول';
                return 'راسب';
            },

            recalculateAll() {
                let gPoints = 0;
                let gHours = 0;
                this.levels.forEach((level) => {
                    let lPoints = 0;
                    let lHours = 0;
                    level.semesters.forEach((sem) => {
                        sem.subjects.forEach((sub) => {
                            this.ratingFromScore(sub);
                            const h = this.parseNum(sub.hours);
                            const sc = this.parseNum(sub.score);
                            if (Number.isFinite(h) && h > 0 && Number.isFinite(sc)) {
                                lPoints += sc * h;
                                lHours += h;
                            }
                        });
                    });
                    gPoints += lPoints;
                    gHours += lHours;
                    if (lHours > 0) {
                        level.avg = (lPoints / lHours).toFixed(2);
                        level.totalPoints = lPoints.toFixed(2);
                    } else {
                        level.avg = '';
                        level.totalPoints = '';
                    }
                });
                if (gHours > 0) {
                    const avg = gPoints / gHours;
                    this.student.total = gPoints.toFixed(2);
                    this.student.gpa = `${avg.toFixed(2)}%`;
                    this.student.rating = this.ratingFromAverage(avg);
                } else {
                    this.student.total = '';
                    this.student.gpa = '';
                    this.student.rating = '';
                }
            },

            addSub(lIdx, sIdx) {
                this.levels[lIdx].semesters[sIdx].subjects.push({
                    catalog_key: '',
                    name: '',
                    hours: '',
                    score: '',
                    rating: '',
                });
            },

            removeSub(lIdx, sIdx, subIdx) {
                this.levels[lIdx].semesters[sIdx].subjects.splice(subIdx, 1);
                this.recalculateAll();
            },

            async saveData() {
                if (!this.saveUrl || this.saveUrl === '#') {
                    alert('مسار الحفظ غير مهيأ.');
                    return;
                }
                this.recalculateAll();
                this.saving = true;
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const res = await fetch(this.saveUrl, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token || '',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ student: this.student, levels: this.levels }),
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        let msg = data.message || 'تعذر الحفظ';
                        if (data.errors) {
                            const flat = Object.values(data.errors).flat().join(' ');
                            if (flat) msg = flat;
                        }
                        throw new Error(msg);
                    }
                    alert(data.message || 'تم حفظ السجل الأكاديمي في قاعدة البيانات.');
                } catch (e) {
                    alert(e.message || 'حدث خطأ أثناء الحفظ');
                } finally {
                    this.saving = false;
                }
            },

            resetData() {
                if (confirm('هل تريد إعادة تعيين كافة البيانات في الصفحة؟ لن يُحذف السجل المحفوظ في الخادم حتى تحفظ مرة أخرى.')) {
                    location.reload();
                }
            },
        };
    }
    document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });
</script>
@endsection
