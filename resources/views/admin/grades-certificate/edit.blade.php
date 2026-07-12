@extends('layouts.app')

@section('title', __('app.grades_certificate'))

@section('styles')
    <script defer src="{{ asset('assets/js/alpine.min.js') }}"></script>
    <script src="{{ asset('assets/js/lucide.min.js') }}"></script>
    @include('pdf.documents._styles')
    <style>
        /* Scoped style overrides for interactive form inputs inside the print preview */
        .academic-page-content input {
            width: 100%;
            border: none !important;
            background: transparent !important;
            font-family: inherit !important;
            font-size: inherit !important;
            font-weight: inherit !important;
            color: inherit !important;
            text-align: center !important;
            outline: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .academic-page-content input:focus {
            background-color: #f8fafc !important;
            box-shadow: 0 0 0 1px #cbd5e1 !important;
            border-radius: 2px !important;
        }
        .academic-page-content input.field-readonly {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            cursor: not-allowed;
        }
        .academic-page-content select {
            width: 100% !important;
            border: 1px solid #94a3b8 !important;
            border-radius: 6px !important;
            background: #ffffff !important;
            font-family: inherit !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            outline: none !important;
            cursor: pointer !important;
            text-align: right !important;
            direction: rtl !important;
            padding: 4px 8px !important;
            line-height: 1.5 !important;
            height: 28px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
        }

        .academic-page-content select:focus {
            border-color: #4f46e5 !important;
            background-color: #f8fafc !important;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1) !important;
        }

        .academic-page-content select option {
            font-size: 11px !important;
            color: #0f172a !important;
            padding: 6px 10px !important;
            background-color: #ffffff !important;
        }

        .academic-page-content select option[value=""],
        .academic-page-content select option[value="__custom__"] {
            color: #64748b !important;
            font-weight: normal !important;
            font-style: italic !important;
        }

        /* Student info table padding and input styles */
        .academic-page-content .student-table td {
            padding: 6px 10px !important;
        }

        .student-field-line {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 6px !important;
            direction: rtl !important;
            width: 100% !important;
            white-space: nowrap !important;
        }

        .student-field-label {
            font-weight: 800 !important;
            white-space: nowrap !important;
            color: #1e293b !important;
        }

        .academic-page-content .student-table input {
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            outline: none !important;
            font-weight: 700 !important;
            width: auto !important;
            min-width: 90px !important;
            max-width: 100% !important;
            padding: 2px 4px !important;
            margin: 0 !important;
            color: #0f172a !important;
            text-align: right !important;
        }

        .academic-page-content .student-table input:focus {
            background-color: #f8fafc !important;
            box-shadow: 0 0 0 1px #cbd5e1 !important;
            border-radius: 4px !important;
        }

        .academic-page-content .student-table input.field-readonly {
            background-color: transparent !important;
            color: #475569 !important;
            cursor: not-allowed;
        }
        .academic-page-content .student-table input.field-readonly:focus {
            box-shadow: none !important;
            border: none !important;
            background-color: transparent !important;
        }

        /* Subjects table inputs structure and borders */
        .academic-page-content .subjects-table input {
            width: 100% !important;
            border: 1px solid #cbd5e1 !important;
            background-color: #f8fafc !important;
            font-size: 8px !important;
            padding: 3px 4px !important;
            text-align: center !important;
            border-radius: 3px !important;
        }

        .academic-page-content .subjects-table input:focus {
            border-color: #4f46e5 !important;
            background-color: #ffffff !important;
        }
        .academic-page-content .level-header-text input {
            width: 85px !important;
            display: inline-block !important;
            border: 1px solid #cbd5e1 !important;
            background: #ffffff !important;
            border-radius: 3px !important;
            font-size: 7.5px !important;
            padding: 1px 3px !important;
        }
        
        .academic-page-content .level-result {
            padding: 2.5px 5px !important;
        }
        .academic-page-content .level-result input {
            width: 50px !important;
            font-size: 7.5px !important;
            font-weight: bold !important;
            display: inline-block !important;
        }
        
        /* Floating Toolbar styling */
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
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            position: relative;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-admin::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%) scale(0.8);
            background: #0b2545;
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

        /* Toolbar and responsive layout */
        .academic-toolbar-page {
            font-family: 'Tajawal', 'Cairo', sans-serif;
            color: #1d2d44;
            padding: 1rem;
        }
        .ar-page-header {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(11,37,69,0.06);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.25rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            border-right: 4px solid #b89047;
        }
        .ar-page-header .breadcrumb {
            margin-bottom: 0.35rem;
            font-size: 0.82rem;
        }
        .ar-page-header .breadcrumb a {
            color: #134074;
            text-decoration: none;
            font-weight: 600;
        }
        .ar-page-header .breadcrumb a:hover { color: #b89047; }
        .ar-page-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: #0b2545;
            margin: 0;
        }
        .ar-page-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 0.25rem;
        }
        .ar-btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 1.35rem;
            border: 2px solid #d1d9e6;
            border-radius: 50px;
            background: #ffffff;
            color: #1d2d44;
            font-weight: 700;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .ar-btn-back:hover {
            border-color: #0b2545;
            color: #0b2545;
            box-shadow: 0 8px 30px rgba(11,37,69,0.1);
        }
        .academic-layout-root {
            width: 100%;
            max-width: 210mm;
            margin-inline: auto;
            padding-bottom: 5.5rem;
        }

        .btn-add-sub {
            border: 1px dashed #000000;
            background: transparent;
            cursor: pointer;
            padding: 2px 8px;
            font-size: 6.5px;
            font-weight: bold;
            width: 100%;
            color: #000000;
            text-align: center;
        }
        .btn-add-sub:hover {
            background-color: #f1f5f9;
        }
        .btn-del-row {
            color: #ef4444;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 11px;
            font-weight: bold;
            width: 100%;
            height: 100%;
            display: block;
        }
        .btn-del-row:hover {
            background-color: #fee2e2;
        }

        @media (max-width: 767.98px) {
            .academic-toolbar-page { padding: 0.5rem; }
            .ar-page-header {
                padding: 1rem;
                flex-direction: column;
                align-items: flex-start;
            }
            .ar-page-title { font-size: 1.1rem; }
            .ar-btn-back { width: 100%; justify-content: center; }
            .admin-toolbar {
                padding: 8px 16px;
                gap: 8px;
                bottom: 12px;
                border-radius: 40px;
            }
            .btn-admin {
                width: 40px;
                height: 40px;
            }
        }

        @media screen {
            .academic-layout-root {
                margin-top: 55px !important;
            }
        }

        @media print {
            .no-print, .d-print-none, .admin-toolbar, .ar-page-header, .university-topbar, nav.navbar, .navbar, footer, .toast-container {
                display: none !important;
            }
            body {
                background: #ffffff !important;
            }
            .academic-toolbar-page {
                padding: 0 !important;
            }
            .academic-layout-root {
                padding-bottom: 0 !important;
                margin-top: 0 !important;
            }
            .page-wrap {
                border: 1.5px solid #000000 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                min-height: 270mm !important;
            }
        }

        /* Admin Document Preview Refinements */
        .admin-document-preview-wrapper {
            max-width: 1100px;
            margin: 24px auto;
            background: #ffffff;
            direction: rtl;
        }

        .admin-document-body-frame {
            border: 2px solid #111827;
            padding: 18px 22px;
            background: #ffffff;
            box-sizing: border-box;
            width: 100%;
            margin-bottom: 20px;
        }

        .admin-doc-meta-box {
            width: 220px;
            border: 1.5px solid #111827;
            border-collapse: collapse;
            table-layout: fixed;
            direction: ltr;
            font-size: 12px;
            background: #ffffff;
            margin-left: 0;
            margin-right: auto;
            margin-bottom: 12px;
        }

        .admin-doc-meta-box td {
            padding: 6px 8px;
            border: none;
            white-space: nowrap;
            vertical-align: middle;
        }

        .admin-doc-meta-box .meta-value {
            width: 125px;
            direction: ltr;
            text-align: left;
            unicode-bidi: isolate;
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        .admin-doc-meta-box .meta-label {
            width: 95px;
            direction: rtl;
            text-align: right;
            font-weight: bold;
            background: #f3f4f6;
        }

        .admin-document-signatures-section {
            max-width: 1100px;
            margin: 18px auto 0;
            direction: ltr;
        }

        .admin-document-signatures-section .signature-cell {
            direction: rtl;
            text-align: center;
        }

        .subject-print-only {
            display: none;
        }

        /* Ensure absolute dropdown is not clipped */
        .academic-page-content .subjects-table td,
        .academic-page-content .sem-cell,
        .academic-page-content .semester-table,
        .academic-page-content .sem-grid,
        .academic-page-content .sem-grid td,
        .academic-page-content .level-block,
        .academic-page-content .admin-document-body-frame,
        .academic-page-content .subjects-table {
            overflow: visible !important;
        }

        /* Custom subject picker styling */
        .subject-picker {
            position: relative;
            width: 100%;
            direction: rtl;
        }

        .subject-picker-display,
        .subject-picker-manual-input {
            width: 100% !important;
            height: 28px !important;
            min-height: 28px !important;
            padding: 4px 8px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 4px !important;
            background: #ffffff !important;
            color: #020617 !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            line-height: 1.5 !important;
            text-align: right !important;
            cursor: pointer;
            box-sizing: border-box !important;
        }

        .subject-picker-display.placeholder {
            color: #64748b !important;
            font-weight: 600 !important;
        }

        .subject-picker-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            left: 0;
            z-index: 9999;
            max-height: 240px;
            overflow-y: auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
            margin-top: 2px;
            direction: rtl;
        }

        .subject-picker-search {
            width: 100%;
            padding: 6px 10px;
            border: none;
            border-bottom: 1px solid #cbd5e1;
            font-size: 11px;
            font-weight: 700;
            color: #020617;
            outline: none;
            box-sizing: border-box;
            background: #f8fafc;
        }

        .subject-picker-option {
            padding: 8px 12px;
            min-height: 34px;
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            background: #ffffff;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            line-height: 1.4;
            text-align: right;
        }

        .subject-picker-option:hover,
        .subject-picker-option.active {
            background: #f1f5f9;
            color: #0f172a;
        }

        .subject-picker-manual {
            padding: 8px 12px;
            font-size: 11px;
            font-weight: 700;
            color: #4f46e5;
            background: #f8fafc;
            cursor: pointer;
            border-top: 1px dashed #cbd5e1;
            text-align: center;
        }

        .subject-picker-manual:hover {
            background: #eeebff;
        }

        @media print {
            .subject-print-only {
                display: block !important;
            }

            .admin-document-preview-wrapper {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .admin-document-body-frame {
                border: 1.5px solid #000000 !important;
                padding: 3mm 4mm !important;
                margin-bottom: 3.5mm !important;
            }
            .admin-doc-meta-box {
                border: 1.5px solid #000000 !important;
            }
            .admin-doc-meta-box .meta-label {
                background: #f3f4f6 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
@endsection

@section('content')
@php
    $initial = $initialData ?? ['student' => [], 'levels' => []];
    $saveEndpoint = $saveUrl ?? '#';
    $catalog = $subjectCatalog ?? [];
    
    $previewFaculty = $graduate->graduate->major->faculty->name_ar ?? '---';
    $previewMajor = $graduate->graduate->major->name_ar ?? '---';
@endphp
<div class="academic-toolbar-page">
    {{-- Page Header --}}
    <div class="ar-page-header no-print">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('app.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.graduates') }}">{{ __('app.manage_graduates') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('app.grades_certificate') }}</li>
                </ol>
            </nav>
            <h2 class="ar-page-title">{{ __('app.grades_certificate') }}</h2>
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

    {{-- x-data wrapper for interactive preview form editor --}}
    <div class="academic-layout-root" x-data="documentState({{ \Illuminate\Support\Js::from($initial) }}, {{ \Illuminate\Support\Js::from($saveEndpoint) }}, {{ \Illuminate\Support\Js::from($catalog) }})">
        <div class="academic-page-content">
            <div class="admin-document-preview-wrapper">
                <div class="admin-document-body-frame">
                    {{-- Official 3-Column Header --}}
                    <table class="header-table">
                        <tr>
                            <td class="header-left">
                                <table class="admin-doc-meta-box" dir="ltr">
                                    <tr>
                                        <td class="meta-value" dir="ltr">{{ now()->format('Y-m-d') }}</td>
                                        <td class="meta-label" dir="rtl">تاريخ الإصدار</td>
                                    </tr>
                                    <tr>
                                        <td class="meta-value" dir="{{ isset($documentNumber) ? 'ltr' : 'rtl' }}">
                                            {{ $documentNumber ?? 'يُنشأ تلقائيًا' }}
                                        </td>
                                        <td class="meta-label" dir="rtl">رقم الوثيقة</td>
                                    </tr>
                                    <tr>
                                        <td class="meta-value" dir="{{ isset($requestNumber) ? 'ltr' : 'rtl' }}">
                                            {{ $requestNumber ?? 'يُنشأ تلقائيًا' }}
                                        </td>
                                        <td class="meta-label" dir="rtl">رقم الطلب</td>
                                    </tr>
                                </table>
                            </td>
                        <td class="header-center">
                            <img src="{{ asset('assets/images/university-logo.png') }}" alt="SRU" class="uni-logo">
                            <div class="uni-name-under-logo">جامعة إقليم سبأ</div>
                            <div class="header-doc-title">شهادة الدرجات والتقديرات</div>
                        </td>
                        <td class="header-right">
                            الجمهورية اليمنية<br>
                            وزارة التعليم العالي والبحث العلمي<br>
                            جامعة إقليم سبأ<br>
                            {{ $previewFaculty }}<br>
                            قسم : {{ $previewMajor }}
                        </td>
                    </tr>
                </table>

                {{-- Student Information Panel --}}
                <table class="student-table">
                    <tr>
                        <td style="width: 25%;">
                            <div class="student-field-line">
                                <strong class="student-field-label">عام الالتحاق :</strong>
                                <input type="text" class="important-value" x-model="student.enrollmentYear" inputmode="numeric"
                                    @input="student.enrollmentYear = $event.target.value.replace(/[^0-9]/g,'')">
                            </div>
                        </td>
                        <td style="width: 38%;">
                            <table style="width: 100%; border-collapse: collapse; border: none;">
                                <tr style="border: none;">
                                    <td style="border: none; padding: 0; text-align: right; font-weight: bold; width: 50%;">
                                        <div class="student-field-line">
                                            <strong class="student-field-label">مجموع الدرجات:</strong>
                                            <input type="text" class="important-value" x-model="student.total" style="width: 80px !important; text-align: center !important;">
                                        </div>
                                    </td>
                                    <td style="border: none; padding: 0; text-align: left; font-weight: bold; width: 50%;">
                                        <div class="student-field-line" style="justify-content: flex-end;">
                                            <strong class="student-field-label">المعدل:</strong>
                                            <input type="text" class="field-readonly important-value" readonly tabindex="-1" x-model="student.gpa" style="width: 80px !important; text-align: center !important;">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 37%;">
                            <div class="student-field-line">
                                <strong class="student-field-label">اسم الطالب :</strong>
                                <input type="text" class="important-value" x-model="student.name">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="student-field-line">
                                <strong class="student-field-label">عام التخرج :</strong>
                                <input type="text" class="important-value" x-model="student.gradYear" inputmode="numeric"
                                    @input="student.gradYear = $event.target.value.replace(/[^0-9]/g,'')">
                            </div>
                        </td>
                        <td>
                            <table style="width: 100%; border-collapse: collapse; border: none;">
                                <tr style="border: none;">
                                    <td style="border: none; padding: 0; text-align: right; font-weight: bold; width: 35%;">
                                        <div class="student-field-line">
                                            <strong class="student-field-label">التقدير:</strong>
                                            <input type="text" class="field-readonly important-value" readonly tabindex="-1" x-model="student.rating" style="width: 60px !important; text-align: center !important;">
                                        </div>
                                    </td>
                                    <td style="border: none; padding: 0; text-align: center; font-weight: bold; width: 35%;">
                                        <div class="student-field-line" style="justify-content: center;">
                                            <strong class="student-field-label">مرتبة الشرف:</strong>
                                            <input type="text" class="important-value" :value="hasUnder64() ? '—' : student.honors" @input="student.honors = $event.target.value" :disabled="hasUnder64()" style="width: 60px !important; text-align: center !important;">
                                        </div>
                                    </td>
                                    <td style="border: none; padding: 0; text-align: left; font-weight: bold; width: 30%;">
                                        <div class="student-field-line" style="justify-content: flex-end;">
                                            <strong class="student-field-label">الدور:</strong>
                                            <input type="text" class="important-value" x-model="student.dora" list="dora-suggestions" style="width: 60px !important; text-align: center !important;">
                                        </div>
                                        <datalist id="dora-suggestions">
                                            <option value="يونيو">
                                            <option value="سبتمبر">
                                            <option value="الدور الأول">
                                            <option value="الدور الثاني">
                                        </datalist>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <div class="student-field-line">
                                <strong class="student-field-label">الرقم الجامعي :</strong>
                                <input type="text" class="important-value" x-model="student.id" inputmode="numeric" pattern="[0-9]*"
                                    oninput="if(/[^0-9]/.test(this.value)) this.value=this.value.replace(/[^0-9]/g,'')">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>
                            <div class="student-field-line">
                                <strong class="student-field-label">الدرجة العلمية :</strong>
                                <input type="text" class="important-value" x-model="student.degree">
                            </div>
                        </td>
                    </tr>
                </table>

                {{-- Introductory Paragraph Preview --}}
                <div class="intro-container">
                    <div class="intro-text" style="text-align: center;">
                        <div style="margin-bottom: 2px;">
                            تشهد جامعة إقليم سبأ بأن الطالب/ <strong x-text="student.name"></strong> المقيد بالرقم الجامعي (<strong x-text="student.id"></strong>)
                        </div>
                        <div style="margin-bottom: 2px;">
                            قد التحق بالجامعة في العام الدراسي <strong x-text="student.enrollmentYear"></strong>، بكلية <strong>{{ $previewFaculty }}</strong>، تخصص <strong>{{ $previewMajor }}</strong>،
                        </div>
                        <div style="margin-bottom: 2px;">
                            وقد أكمل بنجاح جميع متطلبات التخرج لنيل درجة <strong x-text="student.degree"></strong>،
                        </div>
                        <div>
                            وتخرج في دور <strong x-text="student.dora"></strong> للعام الجامعي <strong x-text="student.gradYear"></strong>، بمعدل تراكمي <strong x-text="student.gpa"></strong>%، وبتقدير عام <strong x-text="student.rating"></strong><span x-show="student.honors && student.honors !== '—' && student.honors !== 'بدون' && student.honors !== 'لا يوجد'"> — <strong x-text="student.honors"></strong></span>.
                        </div>
                    </div>
                </div>

                {{-- Semester Course Work --}}
                <template x-for="(level, lIdx) in levels" :key="lIdx">
                    <div class="level-block">
                        <table class="level-header-bar">
                            <tr>
                                <td style="width: 20%; text-align: right;">
                                    <strong>التقدير :</strong>
                                    <input type="text" class="level-result-input field-readonly" readonly tabindex="-1" x-model="level.result" style="width: 50px; border: none; font-weight: bold; background: transparent; text-align: right;">
                                </td>
                                <td style="width: 20%; text-align: right;">
                                    <strong>المعدل :</strong>
                                    <span x-text="level.avg || '—'"></span>%
                                </td>
                                <td style="width: 20%; text-align: right;">
                                    <strong>المجموع :</strong>
                                    <span x-text="level.total || '—'"></span>
                                </td>
                                <td style="width: 25%; text-align: right;">
                                    <strong>العام الجامعي :</strong>
                                    <input type="text" x-model="level.year" style="width: 80px; display: inline-block; text-align: center; border: 1px solid #ccc; font-size: 7.5px; padding: 1px;">
                                </td>
                                <td style="width: 15%; text-align: center; background-color: #f1f5f9; font-weight: bold;">
                                    المستوى <span x-text="level.name"></span>
                                </td>
                            </tr>
                        </table>

                        <table class="sem-grid">
                            <tr>
                                <template x-for="(sem, sIdx) in level.semesters" :key="sIdx">
                                    <td :class="sIdx == 0 ? 'first-sem' : 'second-sem'">
                                        <div class="sem-title" x-text="sIdx == 0 ? 'الفصل الدراسي الأول' : 'الفصل الدراسي الثاني'"></div>
                                        <table class="subjects-table">
                                            <colgroup>
                                                <col style="width: 50px;">
                                                <col style="width: 40px;">
                                                <col style="width: 35px;">
                                                <col style="width: auto;">
                                                <col style="width: 20px;">
                                                <col class="d-print-none" style="width: 25px;">
                                            </colgroup>
                                            <thead>
                                                <tr>
                                                    <th>التقدير</th>
                                                    <th>الدرجة</th>
                                                    <th>الساعات</th>
                                                    <th>المقرر</th>
                                                    <th>م</th>
                                                    <th class="d-print-none">ح</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="(sub, subIdx) in sem.subjects" :key="subIdx">
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="field-readonly" readonly tabindex="-1" x-model="sub.rating">
                                                        </td>
                                                        <td>
                                                            <input type="number" x-model="sub.score" min="0" max="100" step="0.01" required @input="recalculateAll();">
                                                        </td>
                                                        <td>
                                                            <input type="text" x-model="sub.hours" :readonly="!!sub.catalog_key" :class="sub.catalog_key ? 'field-readonly' : ''" :tabindex="sub.catalog_key ? -1 : 0" inputmode="decimal" @input="sub.hours = $event.target.value.replace(/[^0-9.]/g,''); recalculateAll();">
                                                        </td>
                                                        <td class="subj-name">
                                                            <div class="subject-editor-ui d-print-none" 
                                                                 x-data="{ 
                                                                     isManual: !sub.catalog_key && (sub.name || '').trim() !== '',
                                                                     isOpen: false,
                                                                     searchQuery: '',
                                                                     filteredCourses() {
                                                                         const list = availableCourses(lIdx, sIdx, subIdx);
                                                                         const q = this.searchQuery.trim().toLowerCase();
                                                                         if (!q) return list;
                                                                         return list.filter(c => (c.name_ar || '').toLowerCase().includes(q));
                                                                     },
                                                                     selectCourse(c) {
                                                                         sub.catalog_key = c.key;
                                                                         sub.name = c.name_ar;
                                                                         sub.hours = String(c.credit_hours);
                                                                         this.isOpen = false;
                                                                         this.searchQuery = '';
                                                                         recalculateAll();
                                                                     },
                                                                     switchToManual() {
                                                                         this.isManual = true;
                                                                         sub.catalog_key = '';
                                                                         sub.name = '';
                                                                         this.isOpen = false;
                                                                     },
                                                                     switchToCatalog() {
                                                                         this.isManual = false;
                                                                         sub.catalog_key = '';
                                                                         sub.name = '';
                                                                         sub.hours = '';
                                                                         this.isOpen = false;
                                                                         recalculateAll();
                                                                     }
                                                                 }"
                                                                 @click.away="isOpen = false">
                                                                <!-- Custom Select / Dropdown Mode -->
                                                                <div x-show="!isManual" class="subject-picker">
                                                                    <!-- Display Box -->
                                                                    <div class="subject-picker-display" :class="!sub.name ? 'placeholder' : ''" @click="isOpen = !isOpen; if(isOpen) { searchQuery = ''; $nextTick(() => $refs.searchInput.focus()); }">
                                                                        <span x-text="sub.name || '— اختر المادة —'"></span>
                                                                    </div>

                                                                    <!-- Custom Dropdown -->
                                                                    <div class="subject-picker-dropdown" x-show="isOpen" style="display: none;" @click.stop>
                                                                        <!-- Search Box -->
                                                                        <input type="text" class="subject-picker-search" x-ref="searchInput" x-model="searchQuery" placeholder="ابحث باسم المادة..." @keydown.enter.prevent>
                                                                        
                                                                        <!-- Options List -->
                                                                        <div style="max-height: 200px; overflow-y: auto;">
                                                                            <div class="subject-picker-option" 
                                                                                 @click="selectCourse({ key: '', name_ar: '', credit_hours: '' })"
                                                                                 style="color: #64748b; font-style: italic; font-weight: 600;">
                                                                                — إزالة اختيار المادة —
                                                                            </div>
                                                                            <template x-for="c in filteredCourses()" :key="c.key">
                                                                                <div class="subject-picker-option" 
                                                                                     :class="sub.catalog_key === c.key ? 'active' : ''"
                                                                                     @click="selectCourse(c)"
                                                                                     x-text="c.name_ar">
                                                                                </div>
                                                                            </template>
                                                                            <template x-if="filteredCourses().length === 0">
                                                                                <div style="padding: 12px; font-size: 13px; color: #64748b; text-align: center; font-weight: 700;">
                                                                                    المادة غير موجودة
                                                                                </div>
                                                                            </template>
                                                                        </div>

                                                                        <!-- Manual Trigger inside dropdown -->
                                                                        <div class="subject-picker-manual" @click="switchToManual()">
                                                                            ✎ إضافة مادة غير موجودة (إدخال يدوي)
                                                                        </div>
                                                                    </div>

                                                                    <div style="margin-top: 3px; text-align: right;">
                                                                        <button type="button" @click="switchToManual()" 
                                                                                class="text-link-secondary" style="font-size: 7.5px; border: none; background: none; color: #4f46e5; cursor: pointer; padding: 0; outline: none;">
                                                                            ✎ إضافة مادة غير موجودة في الدليل
                                                                        </button>
                                                                    </div>
                                                                </div>

                                                                <!-- Manual Mode -->
                                                                <div x-show="isManual">
                                                                    <input type="text" class="subject-picker-manual-input" x-model="sub.name" placeholder="اسم المادة يدوياً">
                                                                    <div style="margin-top: 3px; text-align: right;">
                                                                        <button type="button" @click="switchToCatalog()" 
                                                                                class="text-link-secondary" style="font-size: 7.5px; border: none; background: none; color: #4f46e5; cursor: pointer; padding: 0; outline: none;">
                                                                            ← العودة لقائمة المواد الدليلية
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="subject-print-only" x-text="sub.name || '—'"></div>
                                                        </td>
                                                        <td style="font-weight: bold; background-color: #f1f5f9;" x-text="subIdx + 1"></td>
                                                        <td class="d-print-none" style="background-color: #fff5f5;">
                                                            <button type="button" @click="removeSub(lIdx, sIdx, subIdx)" class="btn-del-row" title="حذف مادة">&times;</button>
                                                        </td>
                                                    </tr>
                                                </template>
                                                <template x-if="sem.subjects.length === 0">
                                                    <tr>
                                                        <td colspan="6" style="text-align: center; color: #999; font-style: italic; font-size: 6.5px; padding: 4px; background: #fafafa;">لا توجد مقررات في هذا الفصل — استخدم «إضافة مادة»</td>
                                                    </tr>
                                                </template>
                                                <tr class="add-btn-row d-print-none">
                                                    <td colspan="6" style="padding: 2px;">
                                                        <button type="button" @click="addSub(lIdx, sIdx)" class="btn-add-sub">+ إضافة مادة</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </template>
                            </tr>
                        </table>
                    </div>
                </template>

                </div> {{-- end of admin-document-body-frame --}}

                {{-- Signatures Section --}}
                <div class="admin-document-signatures-section">
                    <table class="sig-table" style="width: 100%; border-collapse: collapse; border: none; direction: ltr;">
                        <tr>
                            <td class="signature-cell" style="width: 25%;">
                                <div class="sig-title">نائب رئيس الجامعة لشؤون الطلاب</div>
                                <div class="sig-line"></div>
                            </td>
                            <td class="signature-cell" style="width: 25%;">
                                <div class="sig-title">المسجل العام</div>
                                <div class="sig-line"></div>
                            </td>
                            <td class="signature-cell" style="width: 25%;">
                                <div class="sig-title">عميد الكلية</div>
                                <div class="sig-line"></div>
                            </td>
                            <td class="signature-cell" style="width: 25%;">
                                <div class="sig-title">مسجل الكلية</div>
                                <div class="sig-line"></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div> {{-- end of admin-document-preview-wrapper --}}
        </div>

        {{-- Floating Admin Toolbar --}}
        <div class="admin-toolbar d-print-none">
            <button type="button" @click.prevent="resetData()" class="btn-admin btn-reset" data-tooltip="إعادة تعيين" title="إعادة تعيين"><i data-lucide="rotate-ccw"></i></button>
            <button type="button" @click.prevent="window.print()" class="btn-admin btn-print" data-tooltip="طباعة" title="طباعة"><i data-lucide="printer"></i></button>
            @isset($graduate)
                <a href="{{ route('admin.graduates.grades-certificate.download', $graduate) }}" class="btn-admin btn-download" style="background: #2563eb; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" data-tooltip="تحميل PDF" title="تحميل PDF"><i data-lucide="download"></i></a>
            @endisset
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
                if (s < 0 || s > 100) {
                    sub.rating = 'غير صالح';
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
                if (avg < 0 || avg > 100) return 'غير صالح';
                if (avg >= 90) return 'ممتاز';
                if (avg >= 80) return 'جيد جداً';
                if (avg >= 70) return 'جيد';
                if (avg >= 60) return 'مقبول';
                return 'راسب';
            },

            hasUnder64() {
                let found = false;
                this.levels.forEach(level => {
                    level.semesters.forEach(sem => {
                        sem.subjects.forEach(sub => {
                            const sc = this.parseNum(sub.score);
                            if (Number.isFinite(sc) && sc <= 64) {
                                found = true;
                            }
                        });
                    });
                });
                return found;
            },

            recalculateAll() {
                let gPoints = 0;
                let gHours = 0;
                this.levels.forEach((level) => {
                    let lPoints = 0;
                    let lHours = 0;
                    let lTotalScore = 0;
                    let hasSubjects = false;
                    let hasFailedSubject = false;
                    level.semesters.forEach((sem) => {
                        sem.subjects.forEach((sub) => {
                            this.ratingFromScore(sub);
                            const h = this.parseNum(sub.hours);
                            const sc = this.parseNum(sub.score);
                            if (Number.isFinite(sc)) {
                                hasSubjects = true;
                                lTotalScore += sc;
                                if (sc < 60) {
                                    hasFailedSubject = true;
                                }
                            }
                            if (Number.isFinite(h) && h > 0 && Number.isFinite(sc)) {
                                lPoints += sc * h;
                                lHours += h;
                            }
                        });
                    });
                    gPoints += lPoints;
                    gHours += lHours;
                    level.total = lTotalScore > 0 ? lTotalScore.toFixed(2) : '';
                    if (lHours > 0) {
                        level.avg = (lPoints / lHours).toFixed(2);
                    } else {
                        level.avg = '';
                    }

                    // Auto-calculate level result: Pass if avg >= 60 and no subject is under 60.
                    if (hasSubjects && lHours > 0) {
                        const avgVal = parseFloat(level.avg);
                        if (avgVal >= 60 && !hasFailedSubject) {
                            level.result = 'ناجح';
                        } else {
                            level.result = 'راسب';
                        }
                    } else {
                        level.result = '';
                    }
                });
                if (gHours > 0) {
                    const avg = gPoints / gHours;
                    this.student.total = gPoints.toFixed(2);
                    this.student.gpa = `${avg.toFixed(2)}%`;
                    this.student.rating = this.ratingFromAverage(avg);

                    // Default honors rank if not set and no disqualifier grade
                    if (!this.hasUnder64()) {
                        if (!this.student.honors || this.student.honors === '—' || this.student.honors.trim() === '') {
                            this.student.honors = 'مستحق';
                        }
                    }
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

            validateData() {
                const errors = [];
                this.levels.forEach((level, lIdx) => {
                    level.semesters.forEach((sem, sIdx) => {
                        sem.subjects.forEach((sub, subIdx) => {
                            const h = sub.hours !== undefined && sub.hours !== null && sub.hours !== '';
                            const s = sub.score !== undefined && sub.score !== null && sub.score !== '';
                            if (h && (!/^[0-9]+(\.[0-9]+)?$/.test(String(sub.hours)) || parseFloat(sub.hours) < 0 || parseFloat(sub.hours) > 30)) {
                                errors.push(`المستوى ${level.name}، مادة ${subIdx+1}: ساعات المادة يجب أن تكون رقماً بين 0 و 30`);
                            }
                            if (s && (!/^[0-9]+(\.[0-9]+)?$/.test(String(sub.score)) || parseFloat(sub.score) < 0 || parseFloat(sub.score) > 100)) {
                                errors.push(`المستوى ${level.name}، مادة ${subIdx+1}: درجة المادة يجب أن تكون رقماً بين 0 و 100`);
                            }
                        });
                    });
                });
                return errors;
            },

            async saveData() {
                if (!this.saveUrl || this.saveUrl === '#') {
                    alert('مسار الحفظ غير مهيأ.');
                    return;
                }
                const validationErrors = this.validateData();
                if (validationErrors.length > 0) {
                    alert('توجد أخطاء في البيانات:\n- ' + validationErrors.join('\n- '));
                    return;
                }
                this.recalculateAll();
                if (this.hasUnder64()) {
                    this.student.honors = '';
                }
                let honorsToSend = this.student.honors;
                if (honorsToSend === 'مستحق' || honorsToSend === 'مستحقة') {
                    honorsToSend = 'مع مرتبة الشرف';
                }
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
                        body: JSON.stringify({ student: { ...this.student, honors: honorsToSend }, levels: this.levels }),
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
                    alert(data.message || 'تم حفظ شهادة الدرجات والتقديرات بنجاح.');
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
