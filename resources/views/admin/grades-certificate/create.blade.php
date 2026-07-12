<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شهادة درجات وتقديرات - إدخال البيانات</title>

    <!-- تمت إزالة خطوط جوجل للعمل دون إنترنت -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.rtl.min.css') }}">
    <script defer src="{{ asset('assets/js/alpine.min.js') }}"></script>
    <script src="{{ asset('assets/js/lucide.min.js') }}"></script>

    <style>
        :root {
            --border-black: #000;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            color: #000;
        }

        /* A4 Page Layout */
        .certificate-container {
            width: 210mm;
            min-height: 297mm;
            margin: 1rem auto;
            padding: 8mm;
            background: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        /* Official Header Slim */
        .official-header {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            align-items: center;
            border: 1px solid var(--border-black);
            padding: 2px 10px;
            margin-bottom: -1px;
        }

        .h-left {
            text-align: right;
            font-size: 0.65rem;
            font-weight: 700;
        }

        .h-center {
            text-align: center;
        }

        .h-right {
            text-align: left;
            font-size: 0.65rem;
            font-weight: 700;
            font-family: 'Amiri', serif;
        }

        .uni-logo-box {
            display: inline-flex;
            width: 35px;
            height: 35px;
            border: 1px solid #000;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            margin-bottom: 2px;
        }

        .doc-main-title {
            font-size: 0.95rem;
            font-weight: 800;
            border: 1px solid #000;
            padding: 1px 25px;
            background: #eee;
            display: inline-block;
        }

        /* Dynamic Intro Paragraph Box */
        .intro-paragraph-box {
            border: 1px solid var(--border-black);
            padding: 8px 12px;
            font-size: 0.78rem;
            line-height: 1.8;
            margin-bottom: 3px;
        }

        .intro-input {
            border: none;
            border-bottom: 1px dotted #ccc;
            background: transparent;
            outline: none;
            text-align: center;
            font-weight: 800;
            color: #1a4a7c;
        }

        .intro-input:focus {
            border-bottom-color: #1a4a7c;
            background: #fdf2f2;
        }

        /* Level Elements Compact */
        .level-block {
            margin-bottom: 2px;
        }

        .level-head {
            background-color: #eee;
            border: 1px solid var(--border-black);
            display: flex;
            justify-content: space-between;
            padding: 1px 12px;
            font-weight: 800;
            font-size: 0.68rem;
            margin-bottom: -1px;
        }

        .sem-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            border: 1px solid var(--border-black);
        }

        .sem-box {
            border-left: 1px solid var(--border-black);
        }

        .sem-box:last-child {
            border-left: none;
        }

        .sem-title {
            background: #fafafa;
            text-align: center;
            font-weight: 800;
            font-size: 0.65rem;
            border-bottom: 1px solid var(--border-black);
            padding: 1px;
        }

        /* Grades Table Mini */
        .grades-table {
            width: 100%;
            font-size: 0.65rem;
            border-collapse: collapse;
        }

        .grades-table th {
            border: 1px solid #000;
            padding: 1px;
            text-align: center;
            background: #f8fafc;
            font-weight: 800;
        }

        .grades-table td {
            border: 1px solid #000;
            padding: 0;
            height: 18px;
            vertical-align: middle;
            position: relative;
        }

        .grades-table input {
            width: 100%;
            border: none;
            padding: 0 4px;
            font-size: 0.7rem;
            text-align: center;
            outline: none;
            background: transparent;
            height: 100%;
        }

        .col-m {
            width: 22px;
            font-weight: 700;
            background: #f0f0f0;
        }

        .col-h {
            width: 25px;
        }

        .col-s {
            width: 35px;
        }

        .col-r {
            width: 50px;
        }

        .col-del {
            width: 22px;
            text-align: center;
            background: #fffefe;
        }

        /* Actions for adding/deleting items */
        .btn-row-del {
            border: none;
            background: none;
            color: #ef4444;
            width: 100%;
            height: 100%;
            font-weight: bold;
            font-size: 14px;
        }

        .add-row-area {
            text-align: center;
            padding: 1px;
            border-bottom: 1.5px solid #000;
        }

        .btn-row-plus {
            font-size: 0.6rem;
            color: #1a4a7c;
            font-weight: 800;
            cursor: pointer;
            border: 1px dashed #1a4a7c;
            padding: 0 10px;
            border-radius: 4px;
            background: transparent;
        }

        .l-summary {
            display: flex;
            justify-content: space-around;
            border: 1px solid #000;
            border-top: none;
            font-size: 0.65rem;
            font-weight: 800;
            padding: 1px;
            background: #fdfdfd;
        }

        /* Signature Row Compact */
        .sig-row {
            display: flex;
            justify-content: space-around;
            margin-top: 5px;
            border-top: 1px solid #000;
            padding-top: 2px;
            text-align: center;
        }

        .sig-box {
            flex: 1;
        }

        .sig-label {
            font-weight: 800;
            font-size: 0.7rem;
            margin-bottom: 12px;
        }

        .sig-dash {
            border-top: 1px dotted #000;
            width: 60%;
            margin: 0 auto;
        }

        /* Floating Admin Toolbar */
        .admin-float-bar {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            background: rgba(255, 255, 255, 0.98);
            padding: 10px 25px;
            border-radius: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #ddd;
            z-index: 5000;
        }

        .btn-float {
            border: none;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: 0.2s;
        }

        .btn-f-print {
            background: #1a4a7c;
        }

        .btn-f-save {
            background: #10b981;
        }

        .btn-f-reset {
            background: #64748b;
        }

        .btn-float:hover {
            transform: translateY(-3px);
        }

        /* --- PRINT CRITICAL OVERRIDES --- */
        @media print {
            @page {
                size: A4;
                margin: 5mm;
            }

            body {
                background: white;
                padding: 0;
            }

            .certificate-container {
                margin: 0;
                padding: 0;
                width: 100%;
                box-shadow: none;
                border: none;
            }

            .admin-float-bar,
            .col-del,
            .add-row-area,
            .d-print-none {
                display: none !important;
            }

            .level-block {
                break-inside: avoid;
            }

            input {
                border: none !important;
                background: transparent !important;
                color: #000 !important;
            }

            .intro-input {
                border-bottom: none !important;
            }
        }
    </style>
</head>

<body x-data="gradesCertificateManager()">

    <!-- Document Type Selector -->
    <div class="container my-3 d-print-none" style="max-width: 210mm;">
        <div class="card p-3 shadow-sm" style="border: 1px solid #ddd; border-radius: 8px; background: white;">
            <div class="row align-items-center">
                <div class="col-md-5 text-end">
                    <label for="docTypeSelect" class="form-label fw-bold text-secondary mb-1"
                        style="font-size: 0.85rem;">اختر نوع الوثيقة:</label>
                    <select id="docTypeSelect" class="form-select" x-model="document_type"
                        style="font-size: 0.9rem; font-weight: bold; border-color: #1a4a7c; color: #1a4a7c;">
                        <option value="grades_certificate">شهادة درجات وتقديرات (Grades Certificate)</option>
                        <option value="academic_record">السجل الأكاديمي (Academic Record)</option>
                    </select>
                </div>
                <div class="col-md-7 text-start text-muted" style="font-size: 0.8rem;">
                    تتيح لك هذه الأداة اختيار نوع الوثيقة للتبديل التلقائي بين التصاميم والمقدمة الرسمية وتعديل البيانات
                    بسهولة.
                </div>
            </div>
        </div>
    </div>

    <div class="certificate-container">
        <!-- 1) Official Header -->
        <header class="official-header">
            <div class="h-left">
                الجمهـورية اليمنيـة<br>
                جامعـة إقليم سبأ<br>
                <input type="text" class="intro-input border-0 p-0 text-end fw-bold"
                    style="font-size: 0.65rem; width: 100%;" x-model="student.faculty"><br>
                قسم : <input type="text" class="intro-input border-0 p-0 text-end fw-bold"
                    style="font-size: 0.65rem; width: 120px;" x-model="student.major">
            </div>
            <div class="h-center">
                <div class="uni-logo-box" style="border: none; width: auto; height: auto;">
                    <img src="{{ asset('assets/images/university-logo.png') }}" alt="SRU"
                        style="width: 42px; height: 42px; object-fit: contain;">
                </div>
                <br>
                <h1 class="doc-main-title">شهادة درجات و تقديرات</h1>
            </div>
            <div class="h-right">
                Republic of Yemen<br>
                Saba Region University<br>
                <input type="text" class="intro-input border-0 p-0 text-start fw-bold"
                    style="font-size: 0.65rem; width: 100%;" x-model="student.facultyEn"><br>
                Dept: <input type="text" class="intro-input border-0 p-0 text-start fw-bold"
                    style="font-size: 0.65rem; width: 120px;" x-model="student.majorEn">
            </div>
        </header>

        <!-- 2) Dynamic Intro Paragraph (shown when document_type is academic_record) -->
        <section class="intro-paragraph-box" x-show="document_type == 'academic_record'">
            تشهد جامعة إقليم سبأ - <span class="fw-bold" x-text="student.faculty"></span> بأن الطالب /
            <input type="text" class="intro-input" style="width: 200px;" x-model="student.name">
            المولود في
            <input type="text" class="intro-input" style="width: 100px;" x-model="student.birthPlace">
            بتاريخ <input type="text" class="intro-input" style="width: 80px;" x-model="student.birthDate"
                inputmode="numeric" @input="student.birthDate = $event.target.value.replace(/[^0-9]/g,'')">م
            ويحمل الجنسية <input type="text" class="intro-input" style="width: 80px;" x-model="student.nationality">
            التحق بالجامعة في العام الجامعي <input type="text" class="intro-input" style="width: 90px;"
                x-model="student.joinYear">
            برقم قيد <input type="text" class="intro-input" style="width: 90px;" x-model="student.id"
                inputmode="numeric" @input="student.id = $event.target.value.replace(/[^0-9]/g,'')">
            وقد حصل على درجة البكالوريوس في تخصص <input type="text" class="intro-input" style="width: 120px;"
                x-model="student.major">
            في دور <input type="text" class="intro-input" style="width: 60px;" x-model="student.attemptMonth">
            لعام <input type="text" class="intro-input" style="width: 60px;" x-model="student.gradYear"
                inputmode="numeric" @input="student.gradYear = $event.target.value.replace(/[^0-9]/g,'')">م
            بتقدير <input type="text" class="intro-input" style="width: 70px;" x-model="student.rating">
            مع مرتبة الشرف <input type="text" class="intro-input" style="width: 90px;" x-model="student.honors">
            و بمعدل <input type="text" class="intro-input" style="width: 50px;" x-model="student.gpa"> %
            وقد درس المقررات الآتية:
        </section>

        <!-- 2) New Official Grades Certificate Intro (shown when document_type is grades_certificate) -->
        <section class="intro-paragraph-box" x-show="document_type == 'grades_certificate'"
            style="text-align: center; padding: 12px 18px;">
            <div style="font-size: 1.1rem; font-weight: 800; margin-bottom: 4px; color: #000;">جامعة إقليم سبأ</div>
            <div
                style="font-size: 1.0rem; font-weight: 800; margin-bottom: 12px; text-decoration: underline; color: #000;">
                شهادة درجات وتقديرات</div>

            <div style="text-align: right; font-size: 0.82rem; line-height: 2.2; margin-top: 10px;">
                تشهد جامعة إقليم سبأ - <span class="fw-bold" x-text="student.faculty"></span> أن الطالب /
                <input type="text" class="intro-input" style="width: 250px;" x-model="student.name">
                <br>
                رقم القيد: <input type="text" class="intro-input" style="width: 120px;" x-model="student.id"
                    inputmode="numeric" @input="student.id = $event.target.value.replace(/[^0-9]/g,'')">
                &nbsp;&nbsp;&nbsp;&nbsp;
                للعام الجامعي: <input type="text" class="intro-input" style="width: 120px;" x-model="student.joinYear">
                <br>
                وقد حصل على درجة بكالوريوس في تخصص <input type="text" class="intro-input" style="width: 180px;"
                    x-model="student.major">
                <br>
                في دور <input type="text" class="intro-input" style="width: 90px;" x-model="student.attemptMonth">
                &nbsp;&nbsp;&nbsp;&nbsp;
                للعام <input type="text" class="intro-input" style="width: 90px;" x-model="student.gradYear"
                    inputmode="numeric" @input="student.gradYear = $event.target.value.replace(/[^0-9]/g,'')">م
                <br>
                بتقدير: <input type="text" class="intro-input" style="width: 120px;" x-model="student.rating">
                &nbsp;&nbsp;&nbsp;&nbsp;
                وبمعدل: <input type="text" class="intro-input" style="width: 80px;" x-model="student.gpa"> %
            </div>
        </section>

        <!-- 3) Academic Levels and Tables -->
        <template x-for="(level, lIdx) in levels" :key="lIdx">
            <div class="level-block">
                <div class="level-head">
                    <span>المستـوى <span x-text="level.name"></span></span>
                    <span>العام الجامعي: <input type="text" class="text-center font-weight-bold"
                            style="width: 80px; border:none; background:none; font-size: 0.68rem;"
                            x-model="level.year"></span>
                    <span>المعدل %: <input type="text" class="text-center font-weight-bold"
                            style="width: 45px; border:none; background:none; font-size: 0.68rem;"
                            x-model="level.avg"></span>
                </div>
                <div class="sem-grid">
                    <template x-for="(sem, sIdx) in level.semesters" :key="sIdx">
                        <div class="sem-box">
                            <div class="sem-title"
                                x-text="sIdx == 0 ? 'الفصـل الدراسي الأول' : 'الفصـل الدراسي الثاني'"></div>
                            <table class="grades-table text-center">
                                <thead>
                                    <tr>
                                        <!-- Unified Delete UI -->
                                        <th class="col-del d-print-none">ح</th>
                                        <th class="col-m">م</th>
                                        <th class="text-start pe-1">اســـــــــــــــــم الـمـادة</th>
                                        <th class="col-h">س.م</th>
                                        <th class="col-s">الدرجة</th>
                                        <th class="col-r">التقدير</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(sub, subIdx) in sem.subjects" :key="subIdx">
                                        <tr>
                                            <td class="col-del d-print-none">
                                                <button @click="removeSub(lIdx, sIdx, subIdx)" class="btn-row-del"
                                                    title="حذف">&times;</button>
                                            </td>
                                            <td class="col-m" x-text="subIdx + 1"></td>
                                            <td><input type="text" style="text-align: right;" x-model="sub.name"></td>
                                            <td class="col-h"><input type="text" x-model="sub.hours" inputmode="decimal"
                                                    @input="sub.hours = $event.target.value.replace(/[^0-9.]/g,'')">
                                            </td>
                                            <td class="col-s"><input type="number" x-model="sub.score" min="0" max="100" step="0.01" required></td>
                                            <td class="col-r"><input type="text" x-model="sub.rating"></td>
                                        </tr>
                                    </template>
                                    <tr class="add-row-area d-print-none">
                                        <td colspan="6">
                                            <button @click="addSub(lIdx, sIdx)" class="btn-row-plus">+ إضافة
                                                مادة</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
                <div class="l-summary">
                    <span>المجموع: <input type="text"
                            style="width: 40px; border:none; background:none; font-weight:800;"
                            x-model="level.totalPoints"></span>
                    <span>النتيجة النهائية للمستوى: <input type="text"
                            style="width: 80px; border:none; background:none; font-weight:800;"
                            x-model="level.result"></span>
                </div>
            </div>
        </template>

        <!-- 4) Final Signatures Row -->
        <footer class="sig-row">
            <div class="sig-box">
                <div class="sig-label">مسجل الكلية</div>
                <div class="sig-dash"></div>
            </div>
            <div class="sig-box">
                <div class="sig-label">عميد الكلية</div>
                <div class="sig-dash"></div>
            </div>
            <div class="sig-box">
                <div class="sig-label">المسجل العام</div>
                <div class="sig-dash"></div>
            </div>
            <div class="sig-box">
                <div class="sig-label">نائب رئيس الجامعة لشؤون الطلاب</div>
                <div class="sig-dash"></div>
            </div>
        </footer>
    </div>

    <!-- UI Action Floating Bar -->
    <div class="admin-float-bar d-print-none">
        <button @click="resetData()" class="btn-float btn-f-reset" title="إعادة تعيين"><i
                data-lucide="rotate-ccw"></i></button>
        <button @click="window.print()" class="btn-float btn-f-print" title="طباعة الصفحة"><i
                data-lucide="printer"></i></button>
        <button @click="saveData()" class="btn-float btn-f-save" title="حفظ البيانات"><i
                data-lucide="save"></i></button>
    </div>

    <script>
        function gradesCertificateManager() {
            return {
                document_type: 'grades_certificate',
                student: {
                    name: 'اسم الخريج',
                    birthPlace: 'محافظة الميلاد',
                    birthDate: '2000',
                    nationality: 'يمني',
                    joinYear: '2024 / 2023',
                    id: '20240001',
                    faculty: 'الكلية',
                    facultyEn: 'Faculty',
                    major: 'التخصص',
                    majorEn: 'Major',
                    attemptMonth: 'يونيو',
                    gradYear: '2026',
                    rating: 'التقدير',
                    honors: 'مرتبة الشرف',
                    gpa: '85.00'
                },
                levels: [
                    {
                        name: 'الأول', year: '2025/2024', avg: '85.00', totalPoints: '960', result: 'ناجح', semesters: [
                            { subjects: [{ name: 'المادة الأولى', hours: '3', score: '85', rating: 'جيد جدا' }, { name: 'المادة الثانية', hours: '3', score: '87', rating: 'جيد جدا' }, { name: 'المادة الثالثة', hours: '3', score: '94', rating: 'ممتاز' }] },
                            { subjects: [{ name: 'المادة الرابعة', hours: '3', score: '65', rating: 'جيد' }, { name: 'المادة الخامسة', hours: '3', score: '91', rating: 'ممتاز' }] }
                        ]
                    },
                    {
                        name: 'الثاني', year: '2026/2025', avg: '90.00', totalPoints: '1000', result: 'ناجح', semesters: [
                            { subjects: [{ name: 'المادة السادسة', hours: '3', score: '94', rating: 'ممتاز' }, { name: 'المادة السابعة', hours: '3', score: '97', rating: 'ممتاز' }] },
                            { subjects: [{ name: 'المادة الثامنة', hours: '3', score: '93', rating: 'ممتاز' }] }
                        ]
                    },
                    {
                        name: 'الثالث', year: '2027/2026', avg: '86.00', totalPoints: '1030', result: 'ناجح', semesters: [
                            { subjects: [{ name: 'المادة التاسعة', hours: '3', score: '76', rating: 'جيد' }] },
                            { subjects: [{ name: 'المادة العاشرة', hours: '3', score: '96', rating: 'ممتاز' }] }
                        ]
                    },
                    {
                        name: 'الرابع', year: '2028/2027', avg: '84.00', totalPoints: '840', result: 'ناجح', semesters: [
                            { subjects: [{ name: 'المادة الحادية عشر', hours: '3', score: '90', rating: 'ممتاز' }] },
                            { subjects: [{ name: 'المادة الثانية عشر', hours: '3', score: '98', rating: 'ممتاز' }] }
                        ]
                    }
                ],

                addSub(lIdx, sIdx) {
                    this.levels[lIdx].semesters[sIdx].subjects.push({ name: '', hours: '', score: '', rating: '' });
                },
                removeSub(lIdx, sIdx, subIdx) {
                    this.levels[lIdx].semesters[sIdx].subjects.splice(subIdx, 1);
                },
                validateData() {
                    const errors = [];
                    this.levels.forEach((level, lIdx) => {
                        level.semesters.forEach((sem, sIdx) => {
                            sem.subjects.forEach((sub, subIdx) => {
                                if (sub.hours && (!/^[0-9]+(\.[0-9]+)?$/.test(String(sub.hours)) || parseFloat(sub.hours) < 0 || parseFloat(sub.hours) > 30)) {
                                    errors.push(`المستوى ${level.name}، مادة ${subIdx + 1}: ساعات المادة يجب أن تكون رقماً بين 0 و 30`);
                                }
                                if (sub.score && (!/^[0-9]+(\.[0-9]+)?$/.test(String(sub.score)) || parseFloat(sub.score) < 0 || parseFloat(sub.score) > 100)) {
                                    errors.push(`المستوى ${level.name}، مادة ${subIdx + 1}: درجة المادة يجب أن تكون رقماً بين 0 و 100`);
                                }
                            });
                        });
                    });
                    return errors;
                },
                saveData() {
                    const validationErrors = this.validateData();
                    if (validationErrors.length > 0) {
                        alert('توجد أخطاء في البيانات:\n- ' + validationErrors.join('\n- '));
                        return;
                    }
                    alert('تم حفظ بيانات شهادة الدرجات بنجاح!');
                },
                resetData() { if (confirm('إعادة ضبط كافة البيانات؟')) location.reload(); }
            }
        }
        document.addEventListener('DOMContentLoaded', () => { lucide.createIcons(); });
    </script>
</body>

</html>