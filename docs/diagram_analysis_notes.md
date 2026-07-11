# ملاحظات التحليل والتدقيق للنظام الفعلي
**System Analysis & Verification Notes**

تم إجراء فحص دقيق وشامل للنظام الفعلي لبوابة خدمات الخريجين ومقارنته بملف التوثيق الأساسي (README.md) لتحديد الاختلافات والمكونات الحقيقية المطبقة في الكود.

---

## 1. ملف README الذي تم العثور عليه وقراءته
* **المسار:** `C:\Users\RTX\Desktop\myproject\ملفات المشروع\المعدلهgraduates-portal3.5\README.md`
* **الحجم:** 17.48 KB
* **الملخص:** يصف المشروع بنظرة عامة تقليدية ومخطط Gantt ومواصفات حالة استخدام ومخططات نظرية مبسطة تفترض وجود فاعلين اثنين فقط (الخريج والمسؤول) ودورة حياة خطية بسيطة للوثيقة.

---

## 2. الملفات الفنية التي تم فحصها وتدقيقها
تم فحص الملفات التالية في الكود الفعلي لبناء المخططات بدقة متناهية:
* **الملفات العامة للتوجيهات والمهام:**
  * [AGENTS.md](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/AGENTS.md)
  * [PROJECT_STATE.md](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/PROJECT_STATE.md)
  * [NEXT_STEPS.md](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/NEXT_STEPS.md)
* **المسارات (Routes):**
  * [routes/web.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/routes/web.php)
* **النماذج (Models):**
  * [User.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/app/Models/User.php)
  * [Graduate.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/app/Models/Graduate.php)
  * [DocumentRequest.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/app/Models/DocumentRequest.php)
  * [IssuedDocument.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/app/Models/IssuedDocument.php)
  * [DocumentSignature.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/app/Models/DocumentSignature.php)
  * [GraduateAcademicRecord.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/app/Models/GraduateAcademicRecord.php)
  * [GradesCertificate.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/app/Models/GradesCertificate.php)
  * [Job.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/app/Models/Job.php)
  * [JobApplication.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/app/Models/JobApplication.php)
* **المتحكمات والخدمات (Controllers & Services):**
  * [DocumentRequestController.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/app/Http/Controllers/DocumentRequestController.php)
  * [VerificationController.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/app/Http/Controllers/VerificationController.php)
  * [RequestController.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/app/Http/Controllers/Admin/RequestController.php)
  * [SignatureController.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/app/Http/Controllers/Admin/SignatureController.php)
  * [PaymentReviewController.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/app/Http/Controllers/Admin/PaymentReviewController.php)
  * [DocumentSigningService.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/app/Services/DocumentSigningService.php)
  * [DocumentIssuanceService.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/app/Services/DocumentIssuanceService.php)
  * [RequestStatusService.php](file:///c:/Users/RTX/Desktop/myproject/ملفات%20المشروع/المعدلهgraduates-portal3.22/app/Services/RequestStatusService.php)
* **قاعدة البيانات والهجرة (Migrations):**
  * كافة ملفات المجلد `database/migrations` (40 ملفاً).

---

## 3. الفاعلون الحقيقيون والصلاحيات في النظام (Real Actors & Roles)
على عكس ملف README الذي افترض ممثلين اثنين فقط، يحتوي النظام الفعلي على الأدوار التالية:
1. **الخريج (Graduate):** مستخدم ذو صلاحية دور `graduate` يقوم بتقديم الطلبات، ورفع إثبات الدفع، وتتبع حالة طلبه، وتنزيل الوثيقة الصادرة، والتقديم على الوظائف.
2. **المسؤول المالي (Finance Admin):** إداري ذو صلاحية دور `finance_admin` (أو إداري يملك إذن `finance`) يقوم بمراجعة إثباتات الدفع واعتمادها أو رفضها.
3. **المسؤول الأكاديمي (Academic Admin):** إداري ذو إذن `academic` يقوم بإجراء المراجعة الأكاديمية وإدخال وتعديل السجلات الأكاديمية وكشوفات الدرجات.
4. **الموقّع (Signer):** مستخدم إداري يملك مسمى توقيع محدد في حقل `signer_role` في جدول `users`. ويشملون:
   * **في السجل الأكاديمي (Academic Record):**
     1. المختص الأكاديمي
     2. مدير إدارة شؤون الخريجين
     3. مسجل الكلية
     4. عميد الكلية
   * **في شهادة الدرجات (Grades Certificate):**
     1. مسجل الكلية
     2. عميد الكلية
     3. المسجل العام
     4. نائب رئيس الجامعة لشؤون الطلاب
5. **مدير النظام (Super Admin):** إداري ذو صلاحية كاملة وإذن `super` للتحكم بالمستخدمين والكليات وإعدادات التواقيع.
6. **جهة التوظيف (Employer):** مستخدم ذو صلاحية دور `employer` يقوم بنشر فرص العمل واستقبل طلبات التوظيف من الخريجين.
7. **مسؤول التوظيف (Employment Officer):** إداري ذو إذن `employment` يقوم بمراجعة وتعديل طلبات أصحاب العمل والوظائف المعروضة.
8. **جهة التحقق (Verifier):** شخص خارجي (مثل جهة عمل أو جامعة أخرى) يستعرض صفحة التحقق العام باستخدام رمز QR أو رمز التتبع للتحقق من صحة الوثيقة.

---

## 4. الحالات الحقيقية لطلب الوثيقة (Document Request Statuses)
الحالات المعرفة والمستخدمة في الكود الفعلي هي قيم إنجليزية ثابتة (تترجم للعربية للعرض فقط عبر `lang/ar/app.php`):
* `SUBMITTED` (تم تقديم الطلب)
* `UNDER_REVIEW` (قيد المراجعة الأكاديمية والمالية)
* `APPROVED` (تمت الموافقة المبدئية والأكاديمية)
* `PENDING_SIGNATURES` (بانتظار التوقيعات التسلسلية الإلكترونية)
* `READY` (جاهزة للإصدار والاعتماد النهائي)
* `ISSUED` (تم الإصدار الفعلي وإتاحة المستند للتحميل)
* `REJECTED` (مرفوض)

---

## 5. الجداول الحقيقية المعتمدة في قاعدة البيانات (Real Database Tables)
تم مطابقة الكيانات في مخطط العلاقات (ERD) مع الجداول الفعلية لقاعدة البيانات كالتالي:
* `users`: الحسابات والصلاحيات وحقل التوقيع الإلكتروني `signature_image` وحقل دور التوقيع `signer_role`.
* `graduates`: بيانات الخريجين الإضافية المرتبطة بالحساب (الرقم الجامعي، التخصص، الهاتف، السيرة الذاتية، الصورة).
* `approved_graduates`: سجل الخريجين المعتمدين والمستندات المخزنة مسبقاً للتحقق من هويتهم ومعدلاتهم عند التسجيل الأول.
* `document_types`: أنواع الوثائق ورسومها وتحديد ما إذا كان الدفع مطلوباً.
* `document_requests`: طلبات الوثائق وتفاصيل الدفع وتتبع الحالة.
* `issued_documents`: الوثائق الصادرة فعلياً المرتبطة بطلب الوثيقة وتخزن الرقم التسلسلي ورمز QR ومسار ملف PDF وتوقيت اكتمال التواقيع `all_signed_at`.
* `document_signatures`: التواقيع الفردية المسجلة إلكترونياً لكل وثيقة مع تفاصيل الموقّع والوقت وعنوان الـ IP.
* `request_status_logs`: سجل الحركات والتحولات التاريخية لحالة الطلب لضمان الشفافية.
* `faculties`: الكليات التابعة للجامعة.
* `majors`: التخصصات الأكاديمية التابعة للكليات.
* `graduate_academic_records`: البيانات العامة للسجل الأكاديمي للخريج (تضم الاسم بالعربي والإنجليزي، المعدل، التقدير العام، إلخ).
* `graduate_academic_levels`: المستويات الدراسية التابعة للسجل الأكاديمي.
* `graduate_academic_semesters`: الفصول الدراسية التابعة للمستوى الدراسي.
* `graduate_academic_subjects`: المواد الدراسية التابعة للفصل الدراسي وعدد ساعاتها ودرجاتها.
* `grades_certificates`: جدول كشوفات الدرجات (موازٍ لجداول السجل الأكاديمي).
* `grades_certificate_levels`, `grades_certificate_semesters`, `grades_certificate_subjects`: هيكلية التابع لكشف الدرجات.
* `portal_jobs`: فرص العمل المضافة من قبل أصحاب العمل.
* `job_applications`: تقديمات الخريجين على فرص العمل وحالتها وتاريخ المقابلات.
* `employers`: بيانات جهات التوظيف (الشركات) وحالتها وتخصصها.
* `events`: الفعاليات والتدريبات التي تنظمها الجامعة.
* `event_registrations`: تسجيل الخريجين في الفعاليات.
* `tickets`, `ticket_messages`: التذاكر والدعم الفني للخريجين.
* `contact_messages`: رسائل التواصل العامة من الزوار.
* `notifications`: الإشعارات المخزنة في قاعدة البيانات.
* `audit_logs`: سجل التدقيق الأمني للعمليات الحساسة.

---

## 6. الفروق والتناقضات بين توثيق README والكود الفعلي (Mismatches)
تم رصد عدة اختلافات جوهرية يجب الانتباه إليها عند تقديم المخططات الأكاديمية:
1. **دورة حياة الطلب وسلسلة التواقيع:**
   * **README:** افترض دورة حياة خطية مباشرة (`SUBMITTED → UNDER REVIEW → APPROVED → READY → ISSUED`).
   * **الكود الفعلي:** يضيف حالة وسيطة حرجة وهي بانتظار التواقيع `PENDING_SIGNATURES` حيث تنتقل الوثيقة بين الموقّعين بالتسلسل. بمجرد توقيع آخر موقّع، يتم توليد ملف PDF وتتحول الحالة تلقائياً إلى `ISSUED` في نموذج الخدمات (`DocumentSigningService`).
   * **التناقض البرمجي في الكود:** يحتوي متحكم التوقيع `SignatureController` على دالة تسمى `approveAndIssue()` تقوم بتحويل الحالة يدوياً إلى `READY` بعد اكتمال التوقيعات. لكن الكود في `DocumentSigningService::sign()` يحول الحالة تلقائياً إلى `ISSUED` مباشرة بعد التوقيع الأخير، مما يعني أن الطلب قد لا يمر بحالة `READY` يدوياً إذا اكتملت التواقيع تلقائياً.
2. **الفصل بين السجل الأكاديمي وكشف الدرجات:**
   * **README:** يعاملهما كنوع مستند فقط.
   * **الكود الفعلي:** يفصل تماماً بين جداول قاعدة البيانات ومسارات التوقيع التابعة لكل منهما؛ فالسجل الأكاديمي يتطلب 4 موقّعين مختلفين تماماً عن الـ 4 موقّعين المطلوبين لشهادة الدرجات.
3. **الدفع المالي الفعلي:**
   * **README:** لم يتطرق لآلية التحقق المالي أو إثباتات الدفع أو وجود فاعل مسؤول مالي.
   * **الكود الفعلي:** يفرض قيوداً صارمة تمنع اعتماد الطلب أو توليد الـ PDF ما لم يتم مراجعة إثبات الدفع واعتماده من قبل `finance_admin`.
4. **نظام الإشعارات الفعلي:**
   * تم تطبيق نظام إشعارات ذكي يرسل إشعارات للمسؤولين الماليين عند رفع إثبات دفع جديد، وإشعارات للموقّع الحالي فقط عند حلول دوره بالتوقيع، وإشعارات للخريج عند اعتماد الدفع أو تغيير الحالة العامة للطلب.
