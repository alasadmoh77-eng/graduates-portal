# تقرير التدقيق الفني الشامل لمشروع بوابة خدمات الخريجين - جامعة إقليم سبأ

**تاريخ التدقيق:** 9 يوليو 2026
**نوع التدقيق:** تدقيق فني كامل (بدون تعديل أي ملف)
**حالة المشروع:** المشروع في مرحلة متقدمة من التطوير، مع وجود ملاحظات تحتاج للمعالجة

---

## 1. ملخص عام عن حالة المشروع

المشروع عبارة عن نظام بوابة خدمات خريجين متكامل مبني بإطار Laravel. النظام يغطي معظم الوظائف المطلوبة: تسجيل الخريجين، طلب الوثائق، مراجعة الدفع، المراجعة الأكاديمية، إصدار PDF، التحقق عبر QR، وإدارة الوظائف. المشروع يتبع بنية MVC منظمة مع Services وDrivers وContracts وEnums وPolicies.

**الإيجابيات الرئيسية:**
- استخدام Service Layer منظم مع StatusService و IssuanceService و TrackingCodeService
- تخزين الملفات الحساسة (إثبات الدفع، PDF) على القرص المحلي (private) وليس العام
- استخدام Contracts/Providers للفصل بين مصادر البيانات الأكاديمية (Database, Excel, API)
- نظام صلاحيات متعدد المستويات (admin, super_admin, academic_admin, finance_admin, employment_officer)
- وجود سجل تدقيق (AuditLog) للعمليات
- وجود نظام إشعارات متكامل
- اختبارات Feature تغطي السيناريوهات الأساسية
- دعم اللغتين العربية والإنجليزية مع RTL

**السلبيات الرئيسية:**
- عدم وجود CRUD كامل للتخصصات (Majors) رغم وجوده للكليات
- Policy وهمية في DocumentRequestPolicy تستخدم graduate_id غير موجود
- بعض حالات DocumentRequest الست لا يوجد لها انتقال عكسي (لا يمكن إعادة فتح طلب مرفوض)
- ملف .env مكشوف في المستودع مع APP_KEY حقيقي
- لا يوجد اختلاف حقيقي بين حالتي READY و ISSUED
- ملفات PDF مؤقتة في public/
- السجل الأكاديمي لكل خريج على حدة وليس linked لقاعدة مواد مركزية
- عدم وجود rate limiting على login
- استخدام SQLite كقاعدة بيانات رئيسية (غير مناسب للإنتاج)

**التقييم العام:**
المشروع جيد ومقنع للمناقشة. يحتاج إلى إصلاح المشاكل الحرجة (إخفاء .env، إزالة الملفات المؤقتة، إصلاح الـPolicy) قبل الرفع على GitHub. يمكن تقديم المتبقي كأعمال مستقبلية.

---

## 2. وصف منطق عمل النظام الحالي

### 2.1 تسجيل الخريج
1. الخريج يدخل رقمه الجامعي في صفحة التسجيل
2. النظام يتحقق من وجود الرقم في جدول approved_graduates عبر AJAX
3. إذا كان الرقم موجوداً، يعرض الاسم والكلية والتخصص
4. الخريج يكمل التسجيل بإدخال البريد الإلكتروني وكلمة المرور ورقم الهاتف
5. عند نجاح التسجيل، يُنشأ حساب في users + سجل في graduates + يُنشأ Major تلقائياً إذا لم يوجد
6. يُرسل إشعار للمشرفين

### 2.2 طلب الوثائق
1. الخريج يختار نوع الوثيقة (السجل الأكاديمي / شهادة الدرجات والتقديرات)
2. يختار اللغة (عربي/إنجليزي) والغرض وطريقة التسليم
3. إذا كانت الوثيقة تتطلب دفع، يجب رفع إثبات الدفع
4. يُنشأ الطلب بحالة SUBMITTED مع tracking code فريد

### 2.3 مراجعة الدفع (الموظف المالي)
1. الموظف المالي يشاهد الطلبات بحالة payment_status = pending_review
2. يمكنه مشاهدة إثبات الدفع (ملف مخزن بشكل آمن)
3. يوافق (approved) أو يرفض (rejected)
4. عند الموافقة، تنتقل حالة الطلب من SUBMITTED إلى UNDER_REVIEW تلقائياً

### 2.4 المراجعة الأكاديمية (الموظف الأكاديمي)
1. الموظف الأكاديمي يشاهد الطلبات حسب حالتها
2. يمكنه تغيير حالة الطلب: UNDER_REVIEW → APPROVED أو REJECTED
3. بعد الموافقة (APPROVED)، يمكن توليد PDF → READY → ISSUED

### 2.5 إصدار PDF والتحميل
1. الموظف الأكاديمي ينشئ PDF من خلال DocumentIssuanceService
2. يُنشأ serial number فريد و QR token بطول 64 حرف عشوائي
3. يُخزن PDF في local disk
4. الخريج يحمّل الوثيقة من صفحة طلباته

### 2.6 التحقق عبر QR
1. أي شخص (بدون تسجيل دخول) يدخل رمز التحقق أو serial number
2. النظام يبحث في IssuedDocument
3. يعرض معلومات محدودة: الاسم، التخصص، الكلية، نوع الوثيقة، تاريخ الإصدار
4. الرقم الجامعي يظهر مخفياً جزئياً

### 2.7 نظام الوظائف
1. جهات التوظيف تسجل (تحتاج موافقة employment_officer)
2. تنشر وظائف (تحتاج موافقة إدارية)
3. الخريج يتقدم للوظائف
4. employment_officer يدير العملية

---

## 3. تقييم سير عمل طلب الوثائق

### كيفية عمله حالياً
- الحالات: SUBMITTED → UNDER_REVIEW → APPROVED → READY → ISSUED
- حالة REJECTED هي حالة طرفية
- الانتقالات محكومة بـ RequestStatusService مع transitions map

### التحليل

| # | المشكلة | المكان | الخطورة | التأثير | الإجراء المقترح | إصلاح قبل المناقشة؟ |
|---|---------|--------|----------|---------|-----------------|---------------------|
| 1 | لا يمكن إعادة تقديم طلب مرفوض (REJECTED = Terminal) | `RequestStatusService.php:28` | **High** | الخريج يضطر لإنشاء طلب جديد بدلاً من إعادة تقديم المرفوض | إضافة انتقال REJECTED → SUBMITTED للسماح بإعادة التقديم | نعم |
| 2 | حالتي READY و ISSUED متطابقتان وظيفياً | `RequestStatusService.php:27-29` | **Medium** | غموض في الحالة، لا فرق فعلي بينهما في الكود | إما دمج الحالتين أو جعل ISSUED تعني "تم التحميل من قبل الخريج" | نعم (توضيح للمناقشة) |
| 3 | الانتقال SUBMITTED → UNDER_REVIEW يتم تلقائياً مع الموافقة على الدفع وليس عبر statusService | `PaymentReviewController.php:78-88` | **Medium** | خروج عن نمط StatusService الموحد، تسجيل يدوي للسجل | استخدام statusService->transition داخل PaymentReviewController | لا (تحسين داخلي) |
| 4 | الخريج يمكنه إنشاء أكثر من طلب لنفس نوع الوثيقة بدون قيد | `DocumentRequestController.php:53` | **Medium** | طلبات مكررة وإرباك للمراجعين | إضافة check لوجود طلب نشط (غير مرفوض/جاهز) من نفس النوع | نعم |
| 5 | لا يوجد check على اكتمال السجل الأكاديمي قبل إنشاء الطلب عند الدفع غير مطلوب | `StoreDocumentRequest.php:62-68` | **Low** | طلب وثيقة لا يمكن إصدارها لاحقاً | موجود جزئياً - يتحقق فقط من `hasAcademicRecord` | لا |
| 6 | عدم استخدام الـ Policy في أي Controller | كل الـ Controllers | **Medium** | الصلاحيات تُفحص يدوياً بدل استخدام Gate/Policy | توحيد استخدام Gate::authorize | لا |

### تقييم صحة الانتقالات
خريطة الانتقالات الحالية:
```
SUBMITTED → UNDER_REVIEW
UNDER_REVIEW → APPROVED | REJECTED
APPROVED → READY
READY → ISSUED
REJECTED → (لا شيء - طرفي)
ISSUED → (لا شيء - طرفي)
```

**صحيحة من حيث المبدأ.** لكن ينقصها:
- إمكانية إعادة التقديم بعد الرفض (REJECTED → SUBMITTED)
- إمكانية العودة من APPROVED → UNDER_REVIEW لإعادة المراجعة

---

## 4. تقييم مراجعة الدفع

### نقاط القوة
- تخزين إثبات الدفع في local disk (storage/app/private) وليس public - آمن جداً
- الملف يًُعرض عبر streamDownload فقط من خلال Controller محمي بالصلاحيات
- مسارين منفصلين: مسار الخريج (viewPaymentProof) ومسار الموظف المالي (showProof) - كلاهما يتحقق من الصلاحية
- عند رفض الدفع، يمكن للخريج رفع إثبات جديد (payment_status = rejected → pending_upload → pending_review)
- حجم الملف محدود بـ 5MB
- أنواع الملفات المسموحة: jpg, jpeg, png, pdf

### المشاكل

| # | المشكلة | المكان | الخطورة | التأثير | الإجراء المقترح | إصلاح قبل المناقشة؟ |
|---|---------|--------|----------|---------|-----------------|---------------------|
| 1 | لا يوجد تحقق من نوع الملف الحقيقي (MIME type detection) - يعتمد على extension فقط | `StoreDocumentRequest.php:34` و `DocumentRequestController.php:136` | **High** | يمكن رفع ملف ضار بتغيير extension فقط | إضافة mimes validation مع MIME type detection حقيقي | نعم |
| 2 | الملفات القديمة لا تُحذف عند حذف الطلب (cascade في migration) لكن الملف يبقى على القرص | `0001_02_05_000000` | **Low** | تراكم ملفات غير مستخدمة | إضافة model observer لحذف الملف عند الحذف | لا |
| 3 | حالة payment_status = pending_upload غير معرفة بوضوح في كل الأماكن | `DocumentRequestController.php:131` | **Low** | حالة غير موثقة في migration أو enum | إضافة enum لحالات الدفع | لا |
| 4 | الخريج يمكنه رفع إثبات جديد فقط إذا كان payment_status = rejected أو pending_upload - لكن pending_upload لا يتم تعيينها إلا في حالة رفض سابقة | `DocumentRequestController.php:131` | **Low** | لا يمكن للخريج رفع إثبات جديد ما لم يُرفض أولاً | توثيق هذا السلوك أو توسيعه | لا |

---

## 5. تقييم المراجعة الأكاديمية

### آلية العمل
- الموظف الأكاديمي لديه صلاحية `admin.permission:academic`
- يمكنه عرض جميع الطلبات مع فلاتر status, document_type, language, search, date
- يمكنه تغيير حالة الطلب من خلال updateStatus
- يمكنه توليد PDF من خلال generatePdf

### المشاكل

| # | المشكلة | المكان | الخطورة | التأثير | الإجراء المقترح | إصلاح قبل المناقشة؟ |
|---|---------|--------|----------|---------|-----------------|---------------------|
| 1 | لا يوجد فصل بين من يمكنه "المراجعة الأكاديمية" ومن يمكنه "توليد PDF" | `AdminPermissionMiddleware.php` | **Low** | أي academic_admin يمكنه توليد PDF مباشرة | فصل صلاحية generate-pdf عن update-status | لا |
| 2 | الموظف الأكاديمي لا يرى تنبيه إذا كان السجل الأكاديمي للطالب غير مكتمل قبل محاولة التوليد | `RequestController.php:115` | **Medium** | خطأ فقط عند محاولة التوليد | إظهار تحذير مسبق في صفحة عرض الطلب | لا |
| 3 | لا يوجد validation أن الموظف الأكاديمي لا يمكنه الموافقة على الدفع والعكس | `RequestController.php:90-110` | **Low** | يمكن من حيث المبدأ أن يستخدم الأكاديمي updateStatus ويختار أي حالة | فصل المسارات بوضوح أكثر | لا |
| 4 | لا يوجد check على اكتمال بيانات الخريج الأساسية (مثل major, graduation_year) قبل المراجعة | `RequestController.php` | **Low** | قد يصدر PDF ببيانات ناقصة | إضافة check | لا |

---

## 6. تقييم إصدار PDF والتحقق عبر QR

### 6.1 إصدار PDF

**نقاط القوة:**
- استخدام DomPDF مع دعم الخطوط العربية (Amiri)
- QR code SVG base64 inline
- Serial number بتنسيق SRU-DOC-{YEAR}-{NNNNN}
- QR token عشوائي 64 حرف (آمن جداً)
- PDF يُخزن في local disk (آمن)
- عند إعادة التوليد، يُحافظ على serial number والـQR token الأصليين
- قوالب PDF منفصلة وجميلة مع ترويسة رسمية للجامعة

**المشاكل:**

| # | المشكلة | المكان | الخطورة | التأثير | الإجراء المقترح | إصلاح قبل المناقشة؟ |
|---|---------|--------|----------|---------|-----------------|---------------------|
| 1 | مسار الخط الثابت `C:/dompdf_fonts/Amiri-Regular.ttf` - لن يعمل على سيرفرات أخرى | `_styles.blade.php:13` | **Critical** | فشل تحميل الخط العربي في بيئة غير Windows | استخدام storage_path('fonts/...') فقط أو تضمين الخط في المشروع | نعم |
| 2 | ملف `public/test_grades_certificate.pdf` موجود - ملف مؤقت/تجريبي لا يجب رفعه | `public/` | **High** | ملف غير لائق للرفع على GitHub | حذف الملف | نعم |
| 3 | localhost URLs قد تظهر في PDF إذا كان APP_URL غير مضبوط | `DocumentIssuanceService.php:73` | **Medium** | QR code غير صالح في بيئة الإنتاج | التأكد من APP_URL الصحيح | لا (إعداد بيئة) |
| 4 | ربط DomPDF مباشر بدون try-catch لمشاكل الذاكرة | `DocumentIssuanceService.php:105` | **Medium** | انهيار الصفحة إذا كانت بيانات الطالب كبيرة جداً | إضافة try-catch مع memory limit handling | لا |
| 5 | عند إعادة التوليد، يُحذف الملف القديم ويُنشأ جديد - لا يوجد backup | `DocumentIssuanceService.php:51-53` | **Low** | فقدان الملف القديم في حالة فشل التوليد الجديد | حفظ الملف القديم كمؤقت قبل الحذف | لا |
| 6 | صورة الشعار تستخدم `public_path()` مباشرة وقد لا تعمل إذا كان المسار غير موجود | `layout.blade.php:61` | **Medium** | PDF بدون شعار الجامعة | التحقق من وجود الملف قبل تضمينه | لا |

### 6.2 التحقق عبر QR

**نقاط القوة:**
- مسار عام بدون تسجيل دخول
- Rate limiting (30 طلب/دقيقة)
- يدعم البحث بـ QR token أو serial number أو tracking code
- الرقم الجامعي مخفي جزئياً
- يتعامل مع الوثائق غير الصالحة (is_valid=false) أو الملغية

**المشاكل:**

| # | المشكلة | المكان | الخطورة | التأثير | الإجراء المقترح | إصلاح قبل المناقشة؟ |
|---|---------|--------|----------|---------|-----------------|---------------------|
| 1 | لا يوجد check على is_valid في استعلام التحقق - الوثيقة الملغية تظهر "صالحة" إذا كان is_valid=true ولكن يمكن التحقق بسبب عدم فلترة الحالة | `VerificationController.php:17-23` | **High** | وثيقة ملغية قد تظهر كنتيجة في البحث | إضافة where('is_valid', true) للاستعلام | نعم |
| 2 | إذا لم توجد الوثيقة، لا تظهر رسالة واضحة (تظهر "غير صالحة" بدلاً من "غير موجودة") | `VerificationController.php:37` | **Low** | إرباك للمستخدم | تمييز "غير موجودة" عن "ملغية" | لا |
| 3 | لا يوجد validation على شكل token المدخل - قد يكون غير آمن للبحث المرن باستخدام OR on multiple columns | `VerificationController.php:38-42` | **Low** | أداء منخفض مع بيانات كبيرة | إضافة index إضافية أو استخدام UNION | لا |
| 4 | معلومات حساسة مثل التخصص والكلية تظهر علناً في صفحة التحقق - جيد لأنها ليست شديدة الحساسية | `verify.blade.php:50-55` | **Low** | مناسب للتحقق العلني | لا تغيير مطلوب | لا |

---

## 7. تقييم الأدوار والصلاحيات

### الأدوار الحالية
| الدور | الوصول |
|-------|--------|
| super_admin | كل شيء |
| admin | كل شيء (متوافق مع super_admin) |
| academic_admin | المراجعة الأكاديمية، التقارير، السجل الأكاديمي |
| finance_admin | مراجعة الدفع فقط |
| employment_officer | إدارة جهات التوظيف والوظائف |
| graduate | لوحة التحكم، الملف الشخصي، طلب الوثائق، الوظائف |
| employer | إدارة الوظائف، التقديمات |

### آلية الحماية
- **RoleMiddleware:** يتحقق من الدور ويطابق (admin يشمل جميع الأدوار الإدارية)
- **AdminPermissionMiddleware:** يتحقق من الصلاحية الدقيقة (finance, academic, employment, super)
- **CheckActiveUser:** يتحقق من أن الحساب نشط

### المشاكل

| # | المشكلة | المكان | الخطورة | التأثير | الإجراء المقترح | إصلاح قبل المناقشة؟ |
|---|---------|--------|----------|---------|-----------------|---------------------|
| 1 | admin و super_admin لهما نفس الصلاحيات بالضبط - لا فرق بينهما | `AdminPermissionMiddleware.php:22` | **Low** | تكرار غير ضروري | إما دمج الدورين أو إعطاء صلاحيات مختلفة | لا |
| 2 | Gate::define في AppServiceProvider يعرف `isAdmin` كـ `role === 'admin'` فقط، بينما RoleMiddleware يعرف admin كمجموعة أدوار - تناقض | `AppServiceProvider.php:31` | **Medium** | Gate::allows('isAdmin') سيفشل لـ super_admin, academic_admin, finance_admin | تصحيح Gate ليشمل جميع الأدوار الإدارية | نعم |
| 3 | DocumentRequestPolicy تستخدم `graduate_id` غير موجود في الجدول (الجدول uses `user_id`) | `DocumentRequestPolicy.php:12` | **Critical** | الـ Policy لن تعمل أبداً بشكل صحيح | إصلاح إلى `$user->id === $documentRequest->user_id` | نعم |
| 4 | لا يوجد Policy منفصل لـ IssuedDocument أو PaymentProof | لا يوجد | **Low** | الصلاحيات مبعثرة في Controllers | إضافة Policies مخصصة | لا |
| 5 | CheckActiveUser middleware غير مسجل في Kernel (لم أشاهده مستخدماً) | - | **Medium** | قد لا يعمل middleware تعطيل الحساب تلقائياً | التأكد من تسجيله في bootstrap/app.php أو Kernel | نعم |

---

## 8. تقييم قاعدة البيانات والعلاقات

### الجداول الأساسية وتحليلها

| الجدول | العلاقات | حالة المفاتيح | ملاحظات |
|--------|----------|---------------|---------|
| users | → graduates, employers, academic_records, grades_certificates, document_requests, jobs | سليمة | role string بدل enum |
| graduates | ← users (PK = user_id), → majors | cascadeOnDelete للمستخدم | سليمة |
| document_requests | ← users (cascadeOnDelete), ← document_types, → issued_documents, → logs | cascadeOnDelete للمستخدم، constrained للأنواع | payment_status string غير مقيد |
| issued_documents | ← document_requests (unique, cascadeOnDelete) | فريد، cascadeOnDelete | ممتاز |
| faculties | → majors | set null on delete | سليم |
| majors | ← faculties (nullable), → graduates | hasMany غير معرف في Major model | missing hasMany graduates |
| graduate_academic_records | ← users (unique, cascadeOnDelete), → levels | cascade | سليم |
| graduate_academic_levels | ← academic_records (cascade), → semesters | cascade | سليم |
| graduate_academic_semesters | ← levels (cascade), → subjects | cascade | سليم |
| graduate_academic_subjects | ← semesters (cascade) | cascade | لا يوجد ربط بكتالوج المواد |
| grades_certificates | ← users (unique, cascade), → levels | cascade | منفصل عن academic_records (تكرار هيكلي) |
| approved_graduates | → graduates (via university_id) | لا يوجد foreign key - university_id فقط | خطر inconsistency |
| portal_jobs | ← users كـ employer_id (بدون foreign key constraint!) | مقلق - لا يوجد cascade | يجب إضافة foreign key |
| job_applications | ← portal_jobs, ← users كـ graduate_id | لا يوجد foreign key | يجب إضافة foreign key |

### المشاكل

| # | المشكلة | المكان | الخطورة | التأثير | الإجراء المقترح | إصلاح قبل المناقشة؟ |
|---|---------|--------|----------|---------|-----------------|---------------------|
| 1 | portal_jobs لا يوجد foreign key مع users | `0001_02_07_000000` | **High** | إمكانية وجود employer_id غير صالح | إضافة foreign key أو soft delete | نعم (في التقرير) |
| 2 | job_applications لا يوجد foreign key مع jobs أو users | `0001_02_08_000000` | **High** | إمكانية وجود بيانات يتيمة غير مرتبطة | إضافة foreign keys | نعم (في التقرير) |
| 3 | جدول grades_certificates يكرر هيكل graduate_academic_records بالكامل | `2026_06_20_000000` | **Medium** | صيانة مضاعفة، تكرار كود | إما دمجهما أو توثيق سبب الفصل بوضوح | لا (يحتاج إعادة تصميم) |
| 4 | Major model لا يحتوي hasMany('graduates') رغم أن graduates يستخدم major_id | `Major.php` | **Low** | عدم القدرة على eager loading من جهة التخصص | إضافة العلاقة | لا |
| 5 | approved_graduates لا يوجد به foreign key مع أي جدول - الربط بالنص فقط | `2026_06_21_232000` | **Medium** | بيانات غير متناسقة إذا تغير اسم التخصص أو الكلية | إضافة foreign key مع majors والكليات أو تحويله لـ university_id فقط | لا (مقبول لنطاق المشروع) |
| 6 | عدم وجود soft delete في أي جدول - الحذف نهائي | جميع الجداول | **Medium** | فقدان بيانات مع cascadeOnDelete | إضافة SoftDeletes للكيانات المهمة | لا (مستقبلي) |

---

## 9. تقييم السجل الأكاديمي والمواد

### كيفية عمله حالياً
- السجل الأكاديمي يُخزن في الجداول:
  - `graduate_academic_records` - معلومات الطالب العامة
  - `graduate_academic_levels` - المستويات الدراسية
  - `graduate_academic_semesters` - الفصول (دائماً 2 لكل مستوى)
  - `graduate_academic_subjects` - المواد لكل فصل دراسي
- البيانات تُدخل عبر واجهة إدارية معقدة (Alpine.js + نموذج JSON)
- أو عبر Excel import
- يوجد كتالوج مواد مرجعي `AcademicSubjectCatalog` (في config/academic_subject_catalog.php)

### المشاكل

| # | المشكلة | المكان | الخطورة | التأثير | الإجراء المقترح | إصلاح قبل المناقشة؟ |
|---|---------|--------|----------|---------|-----------------|---------------------|
| 1 | المواد تُخزن لكل خريج على حدة (لكل GraduateAcademicSubject سجل مستقل) - لا يوجد جدول موحد للمواد | `graduate_academic_subjects` | **Medium** | تكرار أسماء المواد، صعوبة التحديث الشامل، حجم بيانات كبير | تصميم جدول subjects مركزي مع pivot table | لا (مقبول للمشروع) |
| 2 | كتالوج المواد في config بدلاً من database - غير قابل للإدارة عبر الـ admin panel | `config/academic_subject_catalog.php` | **Medium** | لا يمكن إضافة/تعديل مواد الكتالوج من لوحة التحكم | نقل الكتالوج إلى جدول في قاعدة البيانات | لا (مستقبلي) |
| 3 | المواد غير مربوطة بـ faculties أو majors | جميع جداول المواد | **Medium** | لا يمكن معرفة أي تخصص تنتمي له المادة | إضافة faculty_id و major_id لجدول المواد | لا (مستقبلي) |
| 4 | `hasAcademicRecord` تتحقق من وجود `levels.semesters.subjects` فقط - قد ترجع false إذا كان المستوى موجود بدون مواد | `DatabaseDriver.php:16-18` | **Low** | طلب وثيقة يُرفض رغم وجود record بدون مواد | تحسين الشرط | لا |
| 5 | الفصول دائماً 2 (semester 0 و semester 1) مهما كان عدد المستويات - صحيح أكاديمياً لنظام الفصلين | `AcademicRecordStorageService.php:74` | **Low** | مناسب لمعظم الجامعات اليمنية | جيد | لا |
| 6 | حساب honors يتحقق من الدرجات ≤ 64 فقط ولا يتحقق من المعدل التراكمي | `AcademicRecordStorageService.php:22-35` | **Medium** | غير متوافق مع بعض لوائح الجامعات | إضافة check على GPA أيضاً | لا (حسب لائحة الجامعة) |

### هل هذا مقبول للمشروع؟
نعم. معظم مشاريع التخرج بهذا المستوى تقوم بتخزين المواد لكل طالب على حدة. يمكن توضيح للجنة أن هذا حل مؤقت (workaround) لأن الجامعة لا توفر API موحد للبيانات الأكاديمية، وقد تم تصميم النظام مع Driver pattern (Database, Excel, API) لتسهيل التوسع مستقبلاً.

---

## 10. تقييم الكليات والتخصصات

### ما هو موجود حالياً
- **الكليات (Faculties):** CRUD كامل عبر Admin panel مع تفعيل/تعطيل - 7 كليات محددة مسبقاً
- **التخصصات (Majors):** تنشأ تلقائياً عند تسجيل الخريج إذا لم تكن موجودة - لا يوجد CRUD إداري
- **الربط بينهما:** Major.faculty_id مع set null on delete

### المشاكل

| # | المشكلة | المكان | الخطورة | التأثير | الإجراء المقترح | إصلاح قبل المناقشة؟ |
|---|---------|--------|----------|---------|-----------------|---------------------|
| 1 | لا يوجد CRUD إداري للتخصصات (Majors) - فقط تنشأ تلقائياً | لا يوجد Controller للتخصصات | **High** | لا يمكن إضافة/تعديل/حذف تخصص، تخصصات مكررة قد تنشأ | إضافة MajorController مع CRUD كامل في admin panel | نعم |
| 2 | التخصصات تنشأ بـ name_en = name_ar عند الإنشاء التلقائي | `AuthController.php:103-109` | **Low** | تخصصات بدون اسم إنجليزي صحيح | إضافة حقل name_en في approved_graduates أيضاً | لا |
| 3 | عند حذف كلية، التخصصات المرتبطة يصبح faculty_id = null - لا cascade | `2026_06_18_000000:127` | **Medium** | تخصصات بلا كلية | إما set null أو منع الحذف إذا يوجد تخصصات مرتبطة | لا |
| 4 | التخصصات غير مربوطة بالمواد في الكتالوج | `majors`, `academic_subject_catalog` | **Low** | لا يمكن تصفية المواد حسب التخصص | إضافة major_id في كتالوج المواد مستقبلاً | لا |

### ماذا أقول للجنة؟
"نظام إدارة الكليات مكتمل مع واجهة CRUD كاملة. أما التخصصات فتنشأ تلقائياً من بيانات الخريجين المعتمدين. تطوير CRUD للتخصصات مع ربطها بالمواد هو من الأعمال المستقبلية المخطط لها في المرحلة الثانية من المشروع."

---

## 11. تقييم الواجهات وتجربة المستخدم

### ملاحظات عامة (بدون فحص بصري - من الكود فقط)
- استخدام Bootstrap 5 مع RTL
- وجود Arabic/English switching
- استخدام pagination و filtering

### المشاكل

| # | المشكلة | المكان | الخطورة | التأثير | الإجراء المقترح | إصلاح قبل المناقشة؟ |
|---|---------|--------|----------|---------|-----------------|---------------------|
| 1 | استخدام `public_path('assets/images/university-logo-pdf.png')` - قد لا يوجد الملف | PDF views | **Medium** | PDF بدون شعار | التأكد من وجود assets/images في public | نعم |
| 2 | صفحة `admin.grades-certificate.preview` تعرض View فقط (view('admin.grades-certificate.create')) بدون بيانات | `web.php:271` | **Medium** | صفحة غير مكتملة | إكمال الصفحة أو إخفاؤها | نعم |
| 3 | استخدام base64 SVG للـ QR في PDF - يزيد حجم PDF وقد يسبب مشاكل عرض | `DocumentIssuanceService.php:74` | **Low** | حجم PDF كبير | استخدام PNG base64 بدل SVG | لا |
| 4 | welcome.blade.php يستخدم $latestJobs من Job model مع ->with(['company', 'employer']) - علاقة company غير معرفة مباشرة (هي employer) | `web.php:19` | **Low** | خطأ محتمل إذا لم يعمل الـ with بشكل صحيح | مراجعة naming العلاقات | لا |
| 5 | صفحة employer.pending لا تحتوي على logout - مستخدم مرفوض لا يستطيع الخروج | `web.php:52-54` | **Low** | تجربة مستخدم سيئة | إضافة رابط logout | لا |

---

## 12. تقييم الأمان

### نقاط القوة
- تخزين الملفات الحساسة في local disk (غير قابل للوصول عبر URL)
- CSRF protection موجود افتراضياً
- Rate limiting على verification والـ check-graduate API
- Password hashing مع Bcrypt
- Session regeneration بعد login/logout
- Middleware للتحقق من is_active
- عدم عرض الرقم الجامعي كاملاً في صفحة التحقق

### المشاكل

| # | المشكلة | المكان | الخطورة | التأثير | الإجراء المقترح | إصلاح قبل المناقشة؟ |
|---|---------|--------|----------|---------|-----------------|---------------------|
| 1 | **ملف .env مكشوف مع APP_KEY حقيقي** | `.env` | **Critical** | اختراق كامل للتطبيق إذا تم رفعه على GitHub | إزالة .env من Git وإعادة توليد APP_KEY | نعم - فوراً |
| 2 | **APP_DEBUG=true حتى على السيرفر المحلي** | `.env:4` | **Critical** | كشف أخطاء تفصيلية مع مسارات الملفات للمستخدم | تعيين APP_DEBUG=false | نعم |
| 3 | لا يوجد rate limiting على login - عرضة لهجمات brute force | `web.php:34-35` | **High** | تخمين كلمات المرور | إضافة rate limiter على login | نعم |
| 4 | لا يوجد email verification بعد التسجيل | `AuthController.php:112` | **High** | إمكانية التسجيل ببريد وهمي | إضافة MustVerifyEmail أو اعتماد بريد approved_graduates | نعم |
| 5 | التحقق من الخريج (check-graduate) مكشوف مع rate limiting فقط - لكن يمكن جمع بيانات الخريجين بالتخمين | `web.php:47-48` | **Medium** | تسريب أسماء وأرقام جامعية | زيادة throttle إلى 5 طلب/دقيقة | نعم |
| 6 | لا يوجد CSP headers أو security headers | لا يوجد | **Medium** | XSS risks | إضافة CSP middleware | لا (مستقبلي) |
| 7 | Session encryption معطل (SESSION_ENCRYPT=false) | `.env:21` | **Medium** | بيانات الجلسة مقروءة على السيرفر | تفعيل encryption | لا |
| 8 | عدم وجود confirm password للإجراءات الحساسة (مثل حذف طلب، إلغاء وثيقة) | لا يوجد | **Low** | إجراءات خطيرة بدون تأكيد إضافي | إضافة password confirmation | لا |

---

## 13. تقييم الاختبارات

### الاختبارات الموجودة
يوجد 16 ملف اختبار في `tests/Feature/`:
- `AcademicRecordImportTest.php`
- `AdminAnalyticsTest.php`
- `AuthTest.php`
- `ContactUsEmailTest.php`
- `DocumentPdfSecurityTest.php`
- `DuplicateJobApplicationTest.php`
- `EmployerDirectoryTest.php`
- `EmployerJobSystemTest.php`
- `EmploymentModuleTest.php`
- `ExampleTest.php`
- `FacultyManagementTest.php`
- `GradesCertificateSeparationTest.php`
- `JobFilledTest.php`
- `PaymentProofSecurityTest.php`
- `RolePermissionsSafetyTest.php`
- `StatusWorkflowValidationTest.php`

بالإضافة إلى اختبارات Sprite (E2E) في `testsprite_tests/`.

### تغطية جيدة للسيناريوهات التالية:
- سير عمل طلب الوثيقة والتحقق من الحالات
- أمان إثبات الدفع (مالك ≠ آخر ≠ ضيف)
- منع الطلبات المكررة
- أدوار وصلاحيات
- إحصائيات dashboard
- إدارة الكليات
- نظام الوظائف والتقديم

### المشاكل

| # | المشكلة | المكان | الخطورة | التأثير | الإجراء المقترح | إصلاح قبل المناقشة؟ |
|---|---------|--------|----------|---------|-----------------|---------------------|
| 1 | لا يوجد اختبار لتوليد PDF فعلياً (mock فقط أو skip) | لا يوجد | **High** | عدم التحقق من صحة PDF الناتج | إضافة اختبار PDF generation مع التحقق من المحتوى | نعم |
| 2 | لا يوجد اختبار للتحقق من QR code verification | لا يوجد | **High** | عدم التحقق من صفحة التحقق العامة | إضافة اختبار verify.show مع token صحيح وخاطئ | نعم |
| 3 | لا يوجد Unit tests للمساعدين (AcademicHelper, ArabicReshaper) | لا يوجد | **Medium** | عدم اختبار الدوال المساعدة | إضافة unit tests | لا |
| 4 | اختبارات Sprite (E2E) غير مضمونة التشغيل لأنها تعتمد على Python | `testsprite_tests/` | **Low** | لا يمكن تشغيلها في الـ CI | نقلها إلى Laravel Dusk أو Cypress | لا |
| 5 | لا يوجد اختبار للـ rate limiting | لا يوجد | **Low** | عدم التحقق من عمل الحماية | إضافة اختبار rate limit | لا |
| 6 | لا يوجد استدعاء لـ `php artisan test` في التقرير للتأكد من نجاحها | - | **High** | قد تكون الاختبارات فاشلة | تشغيل الاختبارات والتأكد من نجاحها | نعم |

---

## 14. تقييم جاهزية النشر و GitHub

### المشاكل الحرجة قبل الرفع على GitHub

| # | المشكلة | الإجراء |
|---|---------|---------|
| 1 | `.env` مكشوف | **يجب إضافته إلى .gitignore فوراً** وإعادة توليد APP_KEY |
| 2 | `database/database.sqlite` موجود | **يجب إزالته** قبل الرفع (يحتوي بيانات تجريبية) |
| 3 | `public/test_grades_certificate.pdf` | **يجب حذفه** (ملف تجريبي) |
| 4 | `storage/logs/` قد تحتوي سجلات | **يجب تنظيفها** |
| 5 | `vendor/` و `node_modules/` | التأكد من وجودهم في .gitignore |
| 6 | APP_DEBUG=true | تغييره إلى false قبل الرفع |
| 7 | MAIL_USERNAME/PASSWORD فيها قيم افتراضية | لا مشكلة طالما أنها `your-email@gmail.com` - لكن يجب التأكد |
| 8 | `storage/app/private/` قد يحتوي ملفات | لا تُرفع عادة - التأكد من gitignore |
| 9 | لا يوجد README.md واضح | إضافة README بشرح المشروع وطريقة التشغيل |
| 10 | الاعتماد على SQLite - يجب توثيق أن هذا للإعداد المحلي فقط | توثيق ذلك في README |

---

## 15. أهم المشاكل المكتشفة مرتبة حسب الخطورة

### Critical (يجب إصلاحها فوراً)
1. **ملف .env مكشوف مع APP_KEY حقيقي** - خطر اختراق كامل
2. **APP_DEBUG=true** - كشف معلومات حساسة
3. **DocumentRequestPolicy تستخدم graduate_id غير موجود** - الـ Policy معطلة
4. **مسار الخط C:/dompdf_fonts/ غير موجود على السيرفرات الأخرى** - فشل PDF العربي

### High
5. لا يوجد rate limiting على login - خطر brute force
6. لا يوجد email verification - تسجيل ببريد وهمي
7. لا يمكن إعادة تقديم طلب مرفوض (REJECTED حالة طرفية)
8. التحقق من MIME type للملفات غير كافٍ (extension فقط)
9. التحقق QR لا يفلتر الوثائق الملغية (is_valid)
10. portal_jobs و job_applications بدون foreign keys
11. لا يوجد CRUD للتخصصات (Majors)
12. Gate::define isAdmin لا يشمل جميع الأدوار الإدارية
13. ملف public/test_grades_certificate.pdf يجب حذفه

### Medium
14. الفصل بين READY و ISSUED غير واضح
15. صفحة grades-certificate.preview غير مكتملة
16. CheckActiveUser middleware قد لا يكون مسجلاً
17. الانتقال SUBMITTED→UNDER_REVIEW خارج StatusService
18. جدول grades_certificates يكرر هيكل academic_records
19. كتالوج المواد في config بدل database
20. المواد غير مربوطة بالكليات والتخصصات
21. حساب honors لا يتحقق من GPA

### Low
22. عدم وجود soft deletes
23. عدم وجود سياسة CSP
24. عدم وجود confirm password للإجراءات الحساسة
25. ملفات قديمة لا تُحذف من القرص عند حذف السجل
26. قاعدة بيانات SQLite غير مناسبة للإنتاج

---

## 16. الأخطاء أو النواقص التي يجب عدم إصلاحها الآن حتى لا نخرب المشروع

| # | المشكلة | سبب عدم الإصلاح الآن |
|---|---------|---------------------|
| 1 | **دمج جدولي academic_records و grades_certificates** | سيؤدي لتغيير كامل في هيكل البيانات والـ views والـ services - خطر كبير قبل المناقشة |
| 2 | **نقل كتالوج المواد من config إلى database** | تغيير كبير في آلية العمل مع AcademicRecordStorageService - قد يكسر Excel import |
| 3 | **إعادة هيكلة دور admin/super_admin** | قد يكسر Middleware الحالي ويؤدي لمشاكل وصول غير متوقعة |
| 4 | **تغيير حالة REJECTED من طرفية إلى قابلة لإعادة التقديم بدون دراسة كافية** | يحتاج لتعديل workflow وقواعد العمل - قد يؤدي لثغرات |
| 5 | **إضافة soft deletes لجميع الجداول** | تغيير كبير في migration - قد يكسر cascade behavior |
| 6 | **تحويل المستودع من SQLite إلى MySQL/PostgreSQL** | تغيير بيئة كامل - يحتاج وقت واختبارات مكثفة |
| 7 | **تغيير آلية التخزين من local disk إلى S3** | ليس مطلوباً للمناقشة |

---

## 17. التحسينات المقترحة قبل المناقشة

### يجب تنفيذها (5-10 أيام عمل):
1. **إخفاء .env** - إضافته لـ .gitignore، التأكد من .gitignore يحتوي على كل الملفات الحساسة
2. **تعيين APP_DEBUG=false** في .env.example
3. **إصلاح DocumentRequestPolicy** - تغيير graduate_id إلى user_id
4. **إزالة public/test_grades_certificate.pdf**
5. **إزالة database/database.sqlite من المستودع**
6. **تغيير مسار الخط في _styles.blade.php** من C:/dompdf_fonts/ إلى storage_path('fonts/')
7. **إضافة rate limiting على login**
8. **إصلاح is_valid check في VerificationController** - إضافة where('is_valid', true)
9. **إصلاح Gate::define isAdmin** ليشمل جميع الأدوار الإدارية
10. **تسجيل CheckActiveUser middleware** في bootstrap/app.php
11. **إصلاح الـ with(['company', 'employer']) في welcome route** - company هي employer
12. **تشغيل php artisan test والتأكد من نجاح جميع الاختبارات**
13. **إضافة اختبارات PDF generation و QR verification**
14. **كتابة README.md واضح**

### يفضل تنفيذها (إن أمكن):
15. إضافة CRUD للتخصصات (Majors) في admin panel
16. إضافة إمكانية إعادة تقديم طلب مرفوض
17. إضافة email verification
18. إضافة MIME type detection حقيقي للملفات المرفوعة
19. إكمال صفحة grades-certificate.preview أو إخفاؤها

---

## 18. التحسينات المقترحة بعد المناقشة كأعمال مستقبلية

1. **تحويل قاعدة البيانات من SQLite إلى MySQL/PostgreSQL** مع إعادة تصميم المخطط
2. **تطوير نظام مواد دراسية مركزي** (جدول subjects مع ربط بالكليات والتخصصات)
3. **دمج الكتالوج الأكاديمي في قاعدة البيانات** مع واجهة إدارة CRUD
4. **تطوير بوابة دفع إلكتروني حقيقية** (بدلاً من رفع إثبات الدفع يدوياً)
5. **لوحة تحكم للخريج لإدارة السجل الأكاديمي** الخاصة به (للجامعات التي تسمح)
6. **نظام تنبيهات عبر البريد الإلكتروني و SMS**
7. **تكامل مع API الجامعة الرسمي** لاستيراد بيانات الخريجين مباشرة
8. **تطبيق mobile app** لعرض الوثائق والتحقق منها
9. **نظام توقيع رقمي** على الوثائق الصادرة
10. **خاصية مشاركة الوثيقة** مع جهة خارجية عبر رابط مؤقت
11. **تحسينات الأداء** - caching, queues, database indexing
12. **اختبارات E2E كاملة** باستخدام Laravel Dusk
13. **دعم إصدار الوثائق بتنسيقات متعددة** (بالإضافة لـ PDF)
14. **نظام audit trail متكامل** لكل التعديلات
15. **نظام نسخ احتياطي تلقائي** للملفات والبيانات

---

## 19. أسئلة متوقعة من لجنة المناقشة مع إجابات مختصرة

| السؤال المتوقع | الإجابة المقترحة |
|----------------|------------------|
| لماذا استخدمت SQLite وليس MySQL؟ | SQLite ممتاز للتطوير السريع والعرض التجريبي. النظام مصمم للعمل مع أي قاعدة بيانات عبر Laravel Eloquent، ويمكن التبديل بسهولة عبر تعديل .env. استخدمنا SQLite لتسهيل تجربة اللجنة بدون الحاجة لإعداد قاعدة بيانات. |
| كيف تضمن أمان بيانات الخريجين؟ | الملفات الحساسة (إثبات الدفع، الملفات الصادرة) مخزنة على local disk لا يمكن الوصول إليه عبر الرابط المباشر. نستخدم CSRF، rate limiting، صلاحيات متعددة المستويات. النظام مطبق عليه مبادئ OWASP الأساسية. |
| كيف يعمل التحقق عبر QR؟ | كل وثيقة تصدر برقم تسلسلي ورمز QR token (64 حرف عشوائي). عند مسح QR code يفتح رابط verify/{token}. الصفحة عامة وتظهر معلومات محدودة فقط. الرقم الجامعي يظهر مخفياً جزئياً. |
| هل يدعم النظام اللغة الإنجليزية؟ | نعم، النظام ثنائي اللغة (عربي/إنجليزي) بالكامل. الواجهة، الوثائق، صفحة التحقق - كلها تدعم اللغتين. الترجمة عبر ملفات Laravel lang. |
| لماذا السجل الأكاديمي لكل طالب منفصل وليس مربوط بقاعدة بيانات مركزية؟ | صممنا النظام بمعمارية Driver pattern (Database, Excel, API). في غياب API من الجامعة، قمنا بتخزين البيانات محلياً. النظام جاهز للتكامل مع API الجامعة مستقبلاً بمجرد تفعيل الـ ApiDriver. |
| كيف تمنع تزوير الوثائق؟ | الوثائق تحتوي على: رقم تسلسلي فريد، QR code برمز تحقق 64 حرف، إمكانية سحب/إلغاء الوثيقة من قبل الإدارة. التحقق عبر QR يظهر فوراً إذا كانت الوثيقة ملغية. |
| ما هي حدود النظام الحالية؟ | لا يوجد بوابة دفع إلكتروني (نظام تحويل بنكي)، التخصصات ليس لها CRUD إداري، السجل الأكاديمي غير مربوط بقاعدة المواد المركزية. هذه كلها مخططة كأعمال مستقبلية. |
| كيف تدير الصلاحيات بين الموظفين؟ | 5 أدوار إدارية: super_admin (كامل)، admin (كامل)، academic_admin (أكاديمي فقط)، finance_admin (مالي فقط)، employment_officer (توظيف فقط). الفصل يتم عبر AdminPermissionMiddleware. |
| هل النظام جاهز للاستخدام الفعلي في الجامعة؟ | النظام جاهز للتجربة والاختبار. للاستخدام الفعلي يحتاج: نقل لقاعدة بيانات إنتاجية، ربط API الجامعة، تفعيل email، تحسينات أمان إضافية، وإعدادات سيرفر. |
| ما أصعب تحدي واجهته في المشروع؟ | التحدي الأكبر كان تصميم نظام السجل الأكاديمي ليعمل مع بيانات Excel من الجامعة مع إمكانية التوسع لـ API. الحل كان معماري Driver pattern مع AcademicRecordStorageService للفصل بين مصدر البيانات وطريقة التخزين. |
| أين تستخدم Design Patterns في المشروع؟ | **Strategy/Driver Pattern:** StudentInformationProvider مع DatabaseDriver, ExcelDriver, ApiDriver. **Service Layer:** RequestStatusService, DocumentIssuanceService. **Observer:** عبر Notifications. **Repository ضمني:** عبر Eloquent Models. |
| هل توجد فجوة أمنية في النظام؟ | مثل أي نظام، توجد مخاطر. لكننا عالجنا الأهم: تخزين آمن للملفات، CSRF، rate limiting، صلاحيات. لم نصل لاختبار اختراق كامل وهو عمل مستقبلي. |
| كيف يمكن لجهة خارجية التحقق من وثيقة؟ | تدخل على رابط verify، تدخل الرقم التسلسلي الموجود على الوثيقة، أو تمسح QR code. يظهر اسم الخريج، التخصص، الكلية، نوع الوثيقة، تاريخ الإصدار، وما إذا كانت سارية. |
| لماذا لا يوجد نظام دفع إلكتروني؟ | الجامعة حالياً لا تملك بوابة دفع إلكتروني. صممنا النظام ليعمل بنظام التحويل البنكي مع رفع إثبات الدفع. البنية جاهزة لإضافة بوابة دفع عند توفرها. |

---

## 20. الخلاصة النهائية: هل المشروع جاهز للمناقشة أم لا؟

### الحكم: **جاهز للمناقشة مع ضرورة إصلاح المشاكل الحرجة أولاً**

**نسبة الجاهزية الحالية:** 75%

**بعد إصلاح المشاكل الحرجة والعالية:** 90%

### نقاط القوة التي ستبهر اللجنة:
1. تصميم معماري نظيف (Services, Contracts, Drivers, Middleware, Policies)
2. نظام صلاحيات متعدد المستويات ومدروس
3. آلية تخزين آمن للملفات (local disk, streamDownload)
4. QR verification مع rate limiting
5. دعم ثنائي اللغة كامل
6. اختبارات Feature تغطي السيناريوهات الأساسية
7. PDF احترافي بتنسيق الجامعة الرسمي
8. استخدام Design Patterns بشكل صحيح

### نقاط الضعف التي قد تنتقدها اللجنة:
1. استخدام SQLite (يجب تبريره جيداً)
2. عدم وجود CRUD للتخصصات
3. اعتماد النظام على بيانات معتمدة مسبقاً (approved_graduates) - ليس تكاملاً حياً
4. بعض الصفحات غير مكتملة (grades-certificate.preview)
5. عدم وجود email sending فعلي (MAIL_MAILER=log)

### خطة العمل النهائية:
1. **هذا الأسبوع:** إصلاح المشاكل الحرجة والعالية (الأمان + الـ Policy + ملفات PDF + التخصصات)
2. **الأسبوع القادم:** تشغيل الاختبارات، كتابة README، تحضير العرض التقديمي
3. **قبل الرفع على GitHub:** تنظيف .env و database.sqlite و test_grades_certificate.pdf
4. **أثناء المناقشة:** التركيز على المعمارية، الأمان، تعدد الأدوار، ومرونة النظام للتوسع

---

## ملحق: جدول المشاكل الحرجة الواجب إصلاحها قبل المناقشة

| # | المشكلة | الملف | الإجراء | الوقت المقدر |
|---|---------|------|---------|-------------|
| 1 | .env مكشوف | `.env` | إضافة .env لـ .gitignore، إعادة توليد APP_KEY | 10 دقائق |
| 2 | APP_DEBUG=true | `.env` | تغيير إلى false | دقيقة |
| 3 | DocumentRequestPolicy معطلة | `app/Policies/DocumentRequestPolicy.php:12` | graduate_id → user_id | 5 دقائق |
| 4 | مسار الخط الثابت | `resources/views/pdf/documents/_styles.blade.php:13` | إزالة C:/dompdf_fonts/ | 5 دقائق |
| 5 | public/test_grades_certificate.pdf | `public/` | حذف الملف | دقيقة |
| 6 | is_valid في VerificationController | `app/Http/Controllers/VerificationController.php:17` | إضافة where is_valid = true | 5 دقائق |
| 7 | Gate::define isAdmin | `app/Providers/AppServiceProvider.php:31` | إضافة all admin roles | 5 دقائق |
| 8 | Rate limiting login | `routes/web.php:34-35` | إضافة throttle middleware | 5 دقائق |
| 9 | with(['company', 'employer']) | `routes/web.php:19` | company → employer | 5 دقائق |
| 10 | CheckActiveUser middleware | `bootstrap/app.php` | تسجيل middleware | 5 دقائق |
| 11 | database.sqlite | `database/` | إزالته من Git | 5 دقائق |

**الوقت الإجمالي المقدر للإصلاحات الحرجة:** 2-3 ساعات

---

*انتهى التقرير.*
