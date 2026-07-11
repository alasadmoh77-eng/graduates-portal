@extends('layouts.app')

@section('title', 'استيراد السجلات الأكاديمية (Excel) | Import Academic Records')

@section('content')
<div class="container py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold text-primary mb-0"><i class="fas fa-file-excel me-2"></i> استيراد السجلات الأكاديمية</h2>
            <p class="text-muted mb-0 small">استيراد جماعي للمقررات والدرجات والتقديرات عبر ملفات Excel أو CSV</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.academic-records.template') }}" class="btn btn-gold rounded-pill px-4 fw-bold shadow-sm">
                <i class="fas fa-download me-2"></i> تحميل قالب Excel المعتمد
            </a>
        </div>
    </div>

    <!-- Error/Global message alerts -->
    @if(isset($error))
        <div class="alert alert-danger border-0 shadow-sm p-4 rounded-4 mb-4 animate__animated animate__fadeIn">
            <div class="d-flex align-items-center">
                <i class="fas fa-times-circle text-danger fs-3 me-3"></i>
                <div>
                    <h5 class="fw-bold mb-1 text-danger">حدثت مشكلة أثناء الاستيراد</h5>
                    <p class="mb-0 text-muted">{{ $error }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Import Form -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold text-dark border-bottom pb-3 mb-4"><i class="fas fa-upload me-2 text-primary"></i> رفع ملف البيانات</h5>
                
                <form action="{{ route('admin.academic-records.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="excel_file" class="form-label fw-bold text-muted small">ملف Excel أو CSV المستهدف</label>
                        <div class="border rounded-3 p-3 bg-light text-center cursor-pointer position-relative hover-shadow" style="border-style: dashed !important; border-width: 2px !important;">
                            <input type="file" name="excel_file" id="excel_file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept=".xlsx,.xls,.csv" required onchange="updateFileName(this)">
                            <i class="fas fa-cloud-upload-alt text-primary fs-1 mb-2"></i>
                            <p class="mb-1 text-dark small fw-bold" id="file-name-label">اسحب الملف هنا أو انقر للتصفح</p>
                            <p class="mb-0 text-muted small" style="font-size: 11px;">الصيغ المدعومة: .xlsx, .xls, .csv (الحد الأقصى: 10 ميجابايت)</p>
                        </div>
                        @error('excel_file')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Options -->
                    <div class="mb-4 bg-light p-3 rounded-3 border">
                        <h6 class="fw-bold text-dark mb-2 small"><i class="fas fa-cog me-1 text-primary"></i> خيارات الاستيراد</h6>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="update_student_profile" name="update_student_profile" value="1">
                            <label class="form-check-label text-dark small fw-bold" for="update_student_profile">تحديث الملف الشخصي للطلاب</label>
                            <p class="text-muted mb-0" style="font-size: 10.5px; line-height: 1.4;">عند التفعيل، سيتم استبدال أسماء الطلاب، كلياتهم، وتخصصاتهم بالقيم المكتوبة بملف الـ Excel. إذا تم إلغاء التفعيل، سيتم الاكتفاء بتحديث السجل الأكاديمي والدرجات فقط دون المساس ببياناتهم الشخصية الأساسية المسجلة مسبقاً.</p>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                        <i class="fas fa-check-circle me-2"></i> بدء عملية المعالجة والاستيراد
                    </button>
                </form>
            </div>
        </div>

        <!-- Import instructions / Columns -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold text-dark border-bottom pb-3 mb-4"><i class="fas fa-info-circle me-2 text-primary"></i> تعليمات وهيكل الأعمدة المطلوبة</h5>
                <p class="small text-muted mb-3">يرجى التأكد من تطابق المسميات والترتيب للأعمدة داخل ملف البيانات لتفادي حدوث أخطاء أثناء المعالجة:</p>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-sm small align-middle mb-0">
                        <thead class="table-light text-center font-monospace">
                            <tr>
                                <th>اسم العمود</th>
                                <th>الوصف والنوع</th>
                                <th>شرط القبول</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold text-primary font-monospace">university_id</td>
                                <td>الرقم الجامعي للطالب (قيمة نصية)</td>
                                <td><span class="badge bg-danger">إلزامي</span> يجب وجود حساب للطالب مسبقاً</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-primary font-monospace">student_name</td>
                                <td>اسم الطالب بالكامل (نصي)</td>
                                <td><span class="badge bg-secondary">اختياري</span> يحدث الاسم في حال تفعيل خيار التحديث</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-primary font-monospace">college / department</td>
                                <td>الكلية والقسم / التخصص (نصي)</td>
                                <td><span class="badge bg-secondary">اختياري</span> يحدث تخصص الطالب بالخيار</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-primary font-monospace">level / semester</td>
                                <td>المستوى الدراسي والفصل الدراسي (نصي)</td>
                                <td>مثال: المستوى الأول / الفصل الأول (أو 1 / 2)</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-primary font-monospace">subject_name</td>
                                <td>اسم المقرر الدراسي (نصي)</td>
                                <td><span class="badge bg-danger">إلزامي</span> مثال: برمجة حاسوب 1</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-primary font-monospace">credit_hours</td>
                                <td>ساعات المادة (رقمي)</td>
                                <td><span class="badge bg-danger">إلزامي</span> يجب أن تكون القيمة أكبر من صفر</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-primary font-monospace">score / grade</td>
                                <td>درجة المادة (0-100) والتقدير الحرفي</td>
                                <td><span class="badge bg-danger">إلزامي للدرجة</span> التقدير يحسب تلقائياً إذا ترك فارغاً</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Results block -->
    @if(isset($result))
        <div class="row mt-4 animate__animated animate__fadeInUp">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold text-dark border-bottom pb-3 mb-4"><i class="fas fa-chart-pie me-2 text-primary"></i> تقرير نتائج معالجة الاستيراد</h5>
                    
                    <div class="row g-3 text-center mb-4">
                        <div class="col-md-4">
                            <div class="bg-success bg-opacity-10 p-3 rounded-4 border border-success border-opacity-25 h-100">
                                <span class="d-block text-success small fw-bold">الطلاب المستوردين والمحدثين</span>
                                <span class="fs-2 fw-bold text-success">{{ $result['success_count'] }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-4 border border-primary border-opacity-25 h-100">
                                <span class="d-block text-primary small fw-bold">إجمالي سجلات السلوكيات المعدلة</span>
                                <span class="fs-2 fw-bold text-primary">{{ $result['new_records'] }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-danger bg-opacity-10 p-3 rounded-4 border border-danger border-opacity-25 h-100">
                                <span class="d-block text-danger small fw-bold">الأخطاء والمخالفات المكتشفة</span>
                                <span class="fs-2 fw-bold text-danger">{{ $result['error_count'] }}</span>
                            </div>
                        </div>
                    </div>

                    @if($result['error_count'] > 0)
                        <div class="border-top pt-4">
                            <h6 class="fw-bold text-danger mb-3"><i class="fas fa-exclamation-triangle me-1"></i> قائمة تفاصيل الأخطاء بالملف:</h6>
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-striped table-sm small align-middle mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width: 100px;">الرقم</th>
                                            <th>وصف وتفاصيل الخطأ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($result['errors'] as $index => $errorLine)
                                            <tr>
                                                <td class="font-monospace fw-bold text-muted">{{ $index + 1 }}</td>
                                                <td class="text-danger fw-bold">{{ $errorLine }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-success border-0 p-3 rounded-3 text-center mb-0 fw-bold">
                            <i class="fas fa-check-circle me-1"></i> اكتمل استيراد ومعالجة الملف بالكامل بنجاح تام 100% ودون أي أخطاء!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    function updateFileName(input) {
        var label = document.getElementById('file-name-label');
        if (input.files && input.files.length > 0) {
            label.innerText = "تم اختيار الملف: " + input.files[0].name;
            label.classList.add('text-success');
        } else {
            label.innerText = "اسحب الملف هنا أو انقر للتصفح";
            label.classList.remove('text-success');
        }
    }
</script>
@endsection
