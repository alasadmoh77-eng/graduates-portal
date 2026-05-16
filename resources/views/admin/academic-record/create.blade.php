@extends('layouts.app')

@section('title', __('app.academic_record_entry_title'))

@section('styles')
    <!-- تمت إزالة خطوط جوجل للعمل دون إنترنت -->
    <script defer src="{{ asset('assets/js/alpine.min.js') }}"></script>
    <script src="{{ asset('assets/js/lucide.min.js') }}"></script>
    <style>
        :root {
            --border-color: #000;
            --transcript-max: 210mm;
            --level-gap: 0.75rem;
        }
        .academic-toolbar-page { font-family: 'Tajawal', sans-serif; color: #000; }
        /* Center transcript; reduce wasted horizontal gutter inside Bootstrap .container */
        .academic-layout-root {
            width: 100%;
            max-width: min(var(--transcript-max), 100%);
            margin-inline: auto;
            padding-bottom: 4.5rem;
        }
        .academic-page {
            width: 100%;
            max-width: var(--transcript-max);
            min-height: 297mm;
            margin: 0.5rem auto 0;
            padding: 6mm 7mm 8mm;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }
        .official-header {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            align-items: center;
            border: 1px solid var(--border-color);
            padding: 2px 8px;
            margin-bottom: -1px;
        }
        .header-left { text-align: right; font-size: 0.65rem; font-weight: 700; line-height: 1.2; }
        .header-center { text-align: center; }
        .header-right { text-align: left; font-size: 0.65rem; font-weight: 700; font-family: 'Amiri', serif; line-height: 1.2; }
        .uni-logo-wrapper { display: inline-flex; align-items: center; justify-content: center; width: 35px; height: 35px; border: 1px solid #000; border-radius: 50%; margin-bottom: 2px; }
        .doc-title { font-size: 0.85rem; font-weight: 800; border: 1px solid #000; padding: 1px 15px; background: #eee; margin: 0; display: inline-block; }
        .student-info-box {
            border: 1px solid var(--border-color);
            padding: 0.35rem 0.55rem;
            font-size: 0.7rem;
            margin-bottom: 0.35rem;
        }
        .info-row { display: flex; justify-content: space-between; gap: 8px; margin-bottom: 1px; flex-wrap: wrap; }
        .info-item { display: flex; gap: 4px; flex: 1; border-bottom: 1px dotted #ccc; min-width: 120px; }
        .info-label { font-weight: 800; white-space: nowrap; }
        .info-value { border: none; background: transparent; width: 100%; outline: none; font-size: 0.72rem; }
        .level-container {
            margin-bottom: var(--level-gap);
            border: 1px solid var(--border-color);
            background: #fff;
        }
        .level-header {
            background: linear-gradient(to bottom, #f1f5f9, #e8ecf0);
            border-bottom: 1px solid var(--border-color);
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
            align-items: center;
            gap: 0.5rem 0.75rem;
            padding: 0.4rem 0.65rem;
            font-weight: 800;
            font-size: 0.7rem;
        }
        .level-header > span:first-child { text-align: right; }
        .level-header > span:nth-child(2) { text-align: center; white-space: nowrap; }
        .level-header > span:last-child { text-align: left; }
        .level-header input.level-year-input {
            width: 5.5rem;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
            background: #fff;
            font-size: 0.65rem;
            font-weight: 800;
            text-align: center;
            padding: 2px 4px;
        }
        .semester-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            align-items: stretch;
            gap: 0;
            border-top: none;
        }
        .semester-box {
            border-inline-start: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }
        .semester-box:first-child { border-inline-start: none; }
        .semester-title {
            background: #f8fafc;
            text-align: center;
            font-weight: 800;
            font-size: 0.66rem;
            border-bottom: 1px solid var(--border-color);
            padding: 0.35rem 0.5rem;
            flex-shrink: 0;
        }
        .semester-box .academic-table { flex: 1; }
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
            padding: 3px 2px;
            text-align: center;
            background: #f1f5f9;
            font-weight: 800;
            line-height: 1.2;
        }
        .academic-table td {
            border: 1px solid #000;
            padding: 0;
            min-height: 22px;
            vertical-align: middle;
            position: relative;
        }
        .academic-subject-cell { vertical-align: top !important; padding: 3px 4px !important; }
        .academic-empty-row td {
            padding: 0.5rem 0.4rem !important;
            text-align: center;
            color: #64748b;
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
            min-height: 22px;
        }
        .academic-table select.subject-select {
            width: 100%; max-width: 100%; font-size: 0.62rem; padding: 1px 2px; border: none; background: #fafafa;
            text-align: right; direction: rtl; min-height: 20px;
        }
        .subject-editor-ui { text-align: right; }
        .subject-custom-name { width: 100%; border: none; padding: 2px 4px; font-size: 0.68rem; text-align: right; background: #fffef7; }
        .subject-print-only { display: none; font-size: 0.68rem; text-align: right; padding: 2px 4px; min-height: 18px; }
        .field-readonly, input.field-readonly { background: #f1f5f9 !important; color: #334155 !important; cursor: default; }
        .field-readonly-inline { display: inline-block; min-width: 2.5rem; font-weight: 800; color: #0f172a; }
        .col-m { font-weight: 700; background: #f0f0f0; text-align: center; }
        .col-s, .col-d, .col-t { text-align: center; }
        .col-del { text-align: center; background: #fff5f5; }
        .btn-del-row {
            color: #ef4444; border: none; background: none; font-weight: bold;
            padding: 0; width: 100%; height: 100%; line-height: 1;
            font-size: 14px; display: block;
        }
        .btn-del-row:hover { background-color: #ef4444; color: white; }
        .add-btn-row { text-align: center; padding: 1px; }
        .btn-add-sub { font-size: 0.6rem; color: #1a4a7c; font-weight: 800; cursor: pointer; border: 1px dashed #1a4a7c; padding: 0 10px; border-radius: 4px; background: transparent; }
        .level-sum {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            align-items: center;
            gap: 0.5rem 0.75rem;
            border-top: 1px solid var(--border-color);
            padding: 0.45rem 0.65rem;
            font-size: 0.66rem;
            font-weight: 800;
            background: #f8fafc;
        }
        .level-sum-part {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            flex-wrap: wrap;
            min-width: 0;
        }
        .level-sum-part--points { justify-content: flex-end; text-align: right; }
        .level-sum-part--result { justify-content: flex-start; text-align: right; }
        .level-result-input {
            min-width: 6.5rem;
            flex: 1;
            max-width: 12rem;
            border: 1px solid #cbd5e1 !important;
            border-radius: 4px;
            background: #fff !important;
            font-weight: 800;
            padding: 4px 8px !important;
            text-align: center;
        }
        .signatures-row {
            display: flex; justify-content: space-around; margin-top: 5px;
            border-top: 1px solid #000; padding-top: 2px; text-align: center;
            flex-wrap: wrap;
        }
        .sig-item { flex: 1; min-width: 100px; }
        .sig-title { font-weight: 800; font-size: 0.7rem; margin-bottom: 20px; }
        .sig-line { border-top: 1px dotted #000; width: 60%; margin: 0 auto; }
        .admin-toolbar {
            position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
            display: flex; gap: 10px; background: rgba(255,255,255,0.98);
            padding: 10px 25px; border-radius: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            border: 1px solid #ddd; z-index: 2000;
        }
        .btn-admin { border: none; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; transition: 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
        .btn-print { background: #1a4a7c; }
        .btn-save { background: #10b981; }
        .btn-reset { background: #64748b; }
        .btn-admin:hover:not(:disabled) { transform: scale(1.1); box-shadow: 0 4px 12px rgba(0,0,0,0.3); }
        .btn-admin:disabled { opacity: 0.6; cursor: not-allowed; }
        @media print {
            @page { size: A4; margin: 3mm; }
            body { background: white !important; padding: 0; margin: 0; }
            .academic-layout-root { max-width: none; padding-bottom: 0; }
            .academic-page { margin: 0; padding: 2mm 3mm; width: 100%; max-width: none; box-shadow: none; border: none; }
            .academic-table { table-layout: fixed; }
            .admin-toolbar, .col-del, .add-btn-row, .no-print { display: none !important; }
            .academic-empty-row { display: none !important; }
            .level-container { break-inside: avoid; }
            .official-header, .student-info-box, .level-container { margin-bottom: 1px !important; }
            .level-sum { background: #fff !important; padding: 0.25rem 0.4rem !important; }
            .signatures-row { margin-top: 2px !important; }
            input { border: none !important; background: transparent !important; color: #000; }
            .level-result-input { border: none !important; background: transparent !important; }
            .level-year-input { border: none !important; background: transparent !important; }
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
    <div class="no-print mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none fw-bold">{{ __('app.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.graduates') }}" class="text-decoration-none fw-bold">{{ __('app.manage_graduates') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('app.academic_record_entry_title') }}</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-primary mb-0">{{ __('app.academic_record_entry_title') }}</h2>
            @isset($graduate)
                <p class="text-muted small mb-0 mt-1">
                    {{ __('app.graduate_name') }}: <strong>{{ $graduate->name }}</strong>
                    @if($graduate->graduate?->university_id)
                        — {{ __('app.university_id') }}: <strong>{{ $graduate->graduate->university_id }}</strong>
                    @endif
                </p>
            @endisset
        </div>
        <a href="{{ route('admin.reports.graduates') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
            <i class="fas fa-arrow-right me-2"></i> {{ __('app.cancel') }}
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
                Faculty of IT & Comp. Science<br>
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
                        <span class="field-readonly-inline" x-text="level.totalPoints || '—'"></span>
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
        <button type="button" @click.prevent="resetData()" class="btn-admin btn-reset" title="إعادة تعيين"><i data-lucide="rotate-ccw"></i></button>
        <button type="button" @click.prevent="window.print()" class="btn-admin btn-print" title="طباعة"><i data-lucide="printer"></i></button>
        <button type="button" @click.prevent="saveData()" class="btn-admin btn-save" :disabled="saving" title="حفظ في النظام"><i data-lucide="save"></i></button>
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
