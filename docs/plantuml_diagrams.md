# مخططات UML لبوابة خدمات الخريجين
**Graduation Services Portal - Saba Region University**

يحتوي هذا الملف على كافة مخططات UML الخاصة بالنظام الفعلي لبوابة خدمات الخريجين بجامعة إقليم سبأ، مكتوبة بلغة PlantUML وجاهزة للنسخ المباشر إلى موقع [PlantText](https://www.planttext.com/).

---

## 1. مخطط حالات الاستخدام (Use Case Diagram)
### عنوان المخطط: مخطط حالات الاستخدام لنظام بوابة خدمات الخريجين
يوضح هذا المخطط الفاعلين الفعليين في النظام (الخريج، المسؤول المالي، المسؤول الأكاديمي، الموقّعون، مدير النظام، جهة التوظيف، زائر التحقق، ومسؤول التوظيف) وحالات الاستخدام لكل منهم مع علاقات الاشتمال والامتداد.

```plantuml
@startuml
title مخطط حالات الاستخدام لنظام بوابة خدمات الخريجين
left to right direction

skinparam packageStyle rectangle
skinparam actorStyle awesome

actor "الخريج\n(Graduate)" as graduate
actor "المسؤول المالي\n(Finance Admin)" as finance
actor "المسؤول الأكاديمي\n(Academic Admin)" as academic
actor "الموقّع الإلكتروني\n(Signer)" as signer
actor "مدير النظام\n(Super Admin)" as super_admin
actor "جهة التوظيف\n(Employer)" as employer
actor "مسؤول التوظيف\n(Employment Officer)" as employment_officer
actor "زائر التحقق\n(Verifier)" as verifier

rectangle "بوابة خدمات الخريجين" {
    usecase "تسجيل الدخول\n(Login)" as UC_login
    usecase "تسجيل خريج جديد\n(Register Graduate)" as UC_register_grad
    usecase "تقديم طلب وثيقة\n(Submit Document Request)" as UC_request_doc
    usecase "رفع إثبات الدفع\n(Upload Payment Proof)" as UC_upload_payment
    usecase "متابعة حالة الطلب\n(Track Request Status)" as UC_track_request
    usecase "مراجعة إثبات الدفع\n(Review Payment Proof)" as UC_review_payment
    usecase "المراجعة الأكاديمية\n(Academic Review)" as UC_academic_review
    usecase "إدارة السجل الأكاديمي\n(Manage Academic Record)" as UC_manage_record
    usecase "إرسال الوثيقة للتوقيعات\n(Send for Signatures)" as UC_send_signatures
    usecase "التوقيع الإلكتروني التسلسلي\n(Sequential Signing)" as UC_sign_sequentially
    usecase "إصدار الوثيقة والـ PDF\n(Generate PDF & Issue)" as UC_generate_pdf
    usecase "تحميل الوثيقة\n(Download Document)" as UC_download_pdf
    usecase "التحقق عبر QR\n(Verify via QR)" as UC_verify_qr
    usecase "إدارة الإشعارات\n(Manage Notifications)" as UC_manage_notif
    usecase "إدارة فرص العمل\n(Manage Job Offers)" as UC_manage_jobs
    usecase "التقديم على فرصة عمل\n(Apply for Job)" as UC_apply_job
    usecase "إدارة أصحاب العمل\n(Manage Employers)" as UC_manage_employers
    usecase "إدارة المستخدمين والصلاحيات\n(Manage Users & Permissions)" as UC_manage_users
}

' علاقات الخريج
graduate --> UC_login
graduate --> UC_register_grad
graduate --> UC_request_doc
graduate --> UC_upload_payment
graduate --> UC_track_request
graduate --> UC_download_pdf
graduate --> UC_apply_job
graduate --> UC_manage_notif

' علاقات المسؤول المالي
finance --> UC_login
finance --> UC_review_payment

' علاقات المسؤول الأكاديمي
academic --> UC_login
academic --> UC_academic_review
academic --> UC_manage_record
academic --> UC_send_signatures

' علاقات الموقّع
signer --> UC_login
signer --> UC_sign_sequentially

' علاقات مدير النظام
super_admin --> UC_login
super_admin --> UC_manage_users
super_admin --> UC_generate_pdf

' علاقات جهة التوظيف
employer --> UC_login
employer --> UC_manage_jobs

' علاقات مسؤول التوظيف
employment_officer --> UC_login
employment_officer --> UC_manage_employers

' علاقات زائر التحقق
verifier --> UC_verify_qr

' علاقات الاشتمال والامتداد
UC_request_doc <.. UC_upload_payment : <<extend>> (إذا تطلب دفعاً)
UC_send_signatures ..> UC_sign_sequentially : <<include>>
UC_sign_sequentially ..> UC_generate_pdf : <<include>>
UC_academic_review ..> UC_manage_record : <<include>>
@enduml
```

---

## 2. مخطط التتابع: تقديم طلب وثيقة ورفع إثبات الدفع (Sequence Diagram 1)
### عنوان المخطط: مخطط التتابع لتقديم طلب وثيقة ورفع إثبات الدفع
يوضح هذا المخطط التفاعل بين الخريج، والواجهة، والمتحكم، ونظام الملفات، وقاعدة البيانات، ونظام الإشعارات لإتمام تقديم الطلب ورفع إثبات الدفع وإرسال الإشعار للمسؤول المالي فقط.

```plantuml
@startuml
title مخطط التتابع لتقديم طلب وثيقة ورفع إثبات الدفع
autonumber

actor "الخريج\n(Graduate)" as Graduate
boundary "واجهة الخريج\n(Graduate UI)" as UI
control "DocumentRequestController" as Controller
entity "StudentInformationProvider" as Provider
entity "Storage (Private Disk)" as Storage
entity "DocumentRequest" as RequestModel
database "قاعدة البيانات\n(Database)" as DB
control "نظام الإشعارات\n(Notification System)" as Notif
actor "المسؤول المالي\n(Finance Admin)" as Finance

Graduate -> UI: اختيار نوع الوثيقة واللغة وإدخال البيانات
activate UI
UI -> Controller: store(Request)
activate Controller

Controller -> Provider: hasAcademicRecord(User)
activate Provider
Provider --> Controller: true / false
deactivate Provider

alt السجل الأكاديمي غير متوفر
    Controller --> UI: خطأ: السجل الأكاديمي غير مدخل
    UI --> Graduate: عرض رسالة الخطأ
else السجل الأكاديمي متوفر
    alt الدفع مطلوب (payment_required == true)
        Controller -> Storage: تخزين ملف إثبات الدفع (payment-proofs/)
        activate Storage
        Storage --> Controller: payment_proof_path
        deactivate Storage
    end

    Controller -> RequestModel: create(data)
    activate RequestModel
    RequestModel -> DB: حفظ الطلب (status: SUBMITTED, payment_status: pending_review)
    activate DB
    DB --> RequestModel: تم الحفظ بنجاح
    deactivate DB
    RequestModel --> Controller: كائن الطلب (DocumentRequest)
    deactivate RequestModel

    alt الدفع مطلوب ومرفق
        Controller -> Notif: إرسال إشعار (NewPaymentProofSubmitted)
        activate Notif
        Notif -> Finance: إرسال إشعار للمسؤولين الماليين النشطين فقط
        Notif --> Controller: تم الإرسال
        deactivate Notif
    end

    Controller --> UI: إعادة توجيه مع رسالة نجاح
    deactivate Controller
    UI --> Graduate: عرض تأكيد تقديم الطلب بنجاح
    deactivate UI
end
@enduml
```

---

## 3. مخطط التتابع: مراجعة المالية (Sequence Diagram 2)
### عنوان المخطط: مخطط التتابع لمراجعة إثبات الدفع
يوضح هذا المخطط قيام المسؤول المالي باستعراض إثبات الدفع (الذي يتم تحميله بأمان من القرص الخاص) واتخاذ قرار بالاعتماد أو الرفض وتحديث البيانات وإشعار الخريج.

```plantuml
@startuml
title مخطط التتابع لمراجعة إثبات الدفع
autonumber

actor "المسؤول المالي\n(Finance Admin)" as Finance
boundary "لوحة المالية\n(Finance Dashboard)" as UI
control "PaymentReviewController" as Controller
entity "Storage (Private Disk)" as Storage
entity "DocumentRequest" as RequestModel
database "قاعدة البيانات\n(Database)" as DB
control "نظام الإشعارات\n(Notification System)" as Notif
actor "الخريج\n(Graduate)" as Graduate

Finance -> UI: فتح تفاصيل دفعات الطلبات المعلقة
activate UI
UI -> Controller: showProof(documentRequest)
activate Controller
Controller -> Storage: استرجاع ملف إثبات الدفع
activate Storage
Storage --> Controller: محتوى الملف (Stream)
deactivate Storage
Controller --> UI: عرض الملف بأمان (Download Stream)
UI --> Finance: استعراض صورة/ملف إثبات الدفع
deactivate Controller

alt اعتماد الدفع (Approve)
    Finance -> UI: النقر على "اعتماد الدفع"
    UI -> Controller: approve(documentRequest)
    activate Controller
    Controller -> RequestModel: update([payment_status => approved, status => UNDER_REVIEW])
    activate RequestModel
    RequestModel -> DB: تحديث السجل وكتابة RequestStatusLog
    activate DB
    DB --> RequestModel: تم التحديث
    deactivate DB
    RequestModel --> Controller: تم بنجاح
    deactivate RequestModel
    Controller -> Notif: إرسال إشعار (PaymentProofApproved)
    activate Notif
    Notif -> Graduate: إشعار الخريج باعتماد الدفع وتحول الطلب للمراجعة الأكاديمية
    Notif --> Controller: تم الإرسال
    deactivate Notif
    Controller --> UI: رسالة نجاح الاعتماد
    deactivate Controller
    UI --> Finance: تحديث الواجهة إلى معتمد
else رفض الدفع (Reject)
    Finance -> UI: إدخال سبب الرفض والنقر على "رفض"
    UI -> Controller: reject(Request, documentRequest)
    activate Controller
    Controller -> RequestModel: update([payment_status => rejected])
    activate RequestModel
    RequestModel -> DB: تحديث السجل في قاعدة البيانات
    activate DB
    DB --> RequestModel: تم التحديث
    deactivate DB
    RequestModel --> Controller: تم بنجاح
    deactivate RequestModel
    Controller -> Notif: إرسال إشعار (PaymentProofRejected)
    activate Notif
    Notif -> Graduate: إشعار الخريج برفض الدفع مع توضيح السبب
    Notif --> Controller: تم الإرسال
    deactivate Notif
    Controller --> UI: رسالة نجاح الرفض
    deactivate Controller
    UI --> Finance: تحديث الواجهة إلى مرفوض
    deactivate UI
end
@enduml
```

---

## 4. مخطط التتابع: المراجعة الأكاديمية وتجهيز الوثيقة (Sequence Diagram 3)
### عنوان المخطط: مخطط التتابع للمراجعة الأكاديمية وتجهيز الوثيقة
يوضح المخطط قيام المسؤول الأكاديمي بالتحقق من البيانات الأكاديمية للطالب عبر النظام، واعتماد الطلب، ومن ثم إرساله إلى مسار التوقيعات الإلكترونية التسلسلي.

```plantuml
@startuml
title مخطط التتابع للمراجعة الأكاديمية وتجهيز الوثيقة
autonumber

actor "المسؤول الأكاديمي\n(Academic Admin)" as Academic
boundary "لوحة المراجعة الأكاديمية\n(Academic Dashboard)" as UI
control "RequestController" as Controller
entity "StudentInformationProvider" as Provider
entity "DocumentIssuanceService" as Issuance
entity "RequestStatusService" as StatusService
entity "DocumentSigningService" as Signing
entity "IssuedDocument" as DocModel
database "قاعدة البيانات\n(Database)" as DB
control "نظام الإشعارات\n(Notification System)" as Notif
actor "الموقّع الأول في التسلسل\n(First Signer)" as Signer

Academic -> UI: فتح تفاصيل طلب الوثيقة
activate UI
UI -> Controller: show(documentRequest)
activate Controller
Controller -> Provider: hasAcademicRecord(User)
activate Provider
Provider --> Controller: true
deactivate Provider
Controller --> UI: عرض تفاصيل الطلب وبياناته الأكاديمية
deactivate Controller

Academic -> UI: اعتماد المراجعة الأكاديمية وتحديث الحالة إلى APPROVED
UI -> Controller: updateStatus(Request, documentRequest)
activate Controller
Controller -> StatusService: transition(documentRequest, APPROVED, ...)
activate StatusService
StatusService -> DB: تحديث الحالة إلى APPROVED وكتابة Log
activate DB
DB --> StatusService: تم التحديث
deactivate DB
StatusService -> Notif: إشعار الخريج بالموافقة الأكاديمية (APPROVED)
activate Notif
Notif -> Academic: إرجاع النجاح
deactivate Notif
StatusService --> Controller: نجاح الانتقال
deactivate StatusService
Controller --> UI: تم التحديث بنجاح
deactivate Controller

Academic -> UI: النقر على "إرسال لسير التوقيعات"
UI -> Controller: sendForSignatures(documentRequest)
activate Controller
Controller -> Issuance: initiateDraft(documentRequest, adminId)
activate Issuance
Issuance -> DB: إنشاء سجل مسودة الوثيقة (IssuedDocument)\nوتوليد الرقم التسلسلي ورمز QR والتوكن
activate DB
DB --> Issuance: مسودة الوثيقة (IssuedDocument)
deactivate DB
Issuance -> StatusService: transition(documentRequest, PENDING_SIGNATURES, ...)
activate StatusService
StatusService -> DB: تحديث حالة الطلب إلى PENDING_SIGNATURES
activate DB
DB --> StatusService: تم التحديث
deactivate DB
StatusService --> Issuance: نجاح الانتقال
deactivate StatusService
Issuance -> Signing: notifyCurrentSigner(IssuedDocument)
activate Signing
Signing -> DB: البحث عن الموقّع الأول الفعال في التسلسل
activate DB
DB --> Signing: كائن الموقّع الأول (User)
deactivate DB
Signing -> Notif: إرسال إشعار التوقيع المطلوب (SignatureRequired)
activate Notif
Notif -> Signer: تنبيه بالبريد/اللوحة بوجود وثيقة تتطلب توقيعه
Notif --> Signing: تم الإرسال
deactivate Notif
Signing --> Issuance: تم الإشعار
deactivate Signing
Issuance --> Controller: كائن الوثيقة الصادرة المسودة
deactivate Issuance
Controller --> UI: تم إرسال الوثيقة لسير التوقيعات بنجاح
deactivate Controller
deactivate UI
@enduml
```

---

## 5. مخطط التتابع: التوقيع الإلكتروني التسلسلي (Sequence Diagram 4)
### عنوان المخطط: مخطط التتابع للتوقيع الإلكتروني التسلسلي
يوضح هذا المخطط قيام الموقّعين بالتوقيع بالتسلسل الصارم، حيث يقوم النظام بالتحقق التلقائي من أن الموقّع هو الموقّع الحالي في الترتيب، وإشعار الموقّع التالي فقط، وتحديث الحالة تلقائياً إلى ISSUED بعد توقيع الموقّع الأخير.

```plantuml
@startuml
title مخطط التتابع للتوقيع الإلكتروني التسلسلي
autonumber

actor "الموقّع الحالي\n(Current Signer)" as Signer
boundary "واجهة التوقيعات المعلقة\n(Signatures UI)" as UI
control "SignatureController" as Controller
control "DocumentSigningService" as SigningService
entity "IssuedDocument" as DocModel
entity "DocumentSignature" as SignatureModel
database "قاعدة البيانات\n(Database)" as DB
control "DocumentIssuanceService" as IssuanceService
control "RequestStatusService" as StatusService
control "نظام الإشعارات\n(Notification System)" as Notif
actor "الموقّع التالي في التسلسل\n(Next Signer)" as NextSigner
actor "الخريج\n(Graduate)" as Graduate

Signer -> UI: استعراض الوثائق بانتظار توقيعي
activate UI
UI -> Controller: pendingSignatures(Request)
activate Controller
Controller -> SigningService: getPendingForUser(User)
activate SigningService
SigningService -> DocModel: getCurrentSigner() / remainingSigners()
activate DocModel
DocModel --> SigningService: الموقّع الحالي المطلوب
deactivate DocModel
SigningService --> Controller: قائمة الوثائق الخاصة بدور المستخدم الحالي
deactivate SigningService
Controller --> UI: عرض جدول الوثائق المعلقة للتوقيع
deactivate Controller

Signer -> UI: النقر على زر "توقيع المستند"
UI -> Controller: signDocument(Request, issuedDocument)
activate Controller
Controller -> SigningService: sign(User, issuedDocument, roleTitle, ip)
activate SigningService

SigningService -> SigningService: canSign(User, issuedDocument, roleTitle)
note over SigningService: يتحقق من الصلاحية، النشاط،\nوأن الدور يطابق دور الموقّع الحالي في الترتيب

alt غير مصرح له أو ليس دوره
    SigningService --> Controller: استثناء: ليس دورك الحالي للتوقيع
    Controller --> UI: عرض رسالة خطأ
else مصرح له وهو دوره في التسلسل الحالي
    SigningService -> DB: transaction { create DocumentSignature }
    activate DB
    DB --> SigningService: تم الحفظ والتوقيع
    deactivate DB
    
    SigningService -> SigningService: finalizeIfComplete(issuedDocument)
    activate SigningService
    note over SigningService: يتحقق مما إذا كان هذا هو التوقيع الأخير

    alt يتبقى موقّعون آخرون في السلسلة
        SigningService --> SigningService: لا يتم توليد الـ PDF بعد
        deactivate SigningService
        SigningService -> SigningService: notifyCurrentSigner(issuedDocument)
        activate SigningService
        SigningService -> DB: البحث عن المستخدمين بالدور التالي
        activate DB
        DB --> SigningService: الموقّعون التاليون (Users)
        deactivate DB
        SigningService -> Notif: إرسال إشعار التوقيع المطلوب (SignatureRequired)
        activate Notif
        Notif -> NextSigner: إشعار الموقّع التالي بوجود طلب يتطلب توقيعه
        Notif --> SigningService: تم
        deactivate Notif
        deactivate SigningService
    else اكتملت جميع التوقيعات (التوقيع الأخير)
        SigningService -> IssuanceService: finalizePdf(issuedDocument)
        activate IssuanceService
        note over IssuanceService: توليد ملف الـ PDF النهائي ودمج التواقيع إلكترونياً
        IssuanceService -> DB: تحديث مسار الـ PDF وتاريخ الإصدار وتعيين all_signed_at
        activate DB
        DB --> IssuanceService: تم التحديث
        deactivate DB
        IssuanceService --> SigningService: تم توليد وحفظ الملف
        deactivate IssuanceService
        deactivate SigningService
        
        SigningService -> StatusService: transition(documentRequest, ISSUED, ...)
        activate StatusService
        StatusService -> DB: تحديث حالة الطلب إلى ISSUED
        activate DB
        DB --> StatusService: تم التحديث
        deactivate DB
        StatusService -> Notif: إشعار الخريج بجاهزية الوثيقة للتحميل (ISSUED)
        activate Notif
        Notif -> Graduate: إرسال إشعار تم إصدار الوثيقة
        Notif --> StatusService: تم
        deactivate Notif
        StatusService --> SigningService: تم الانتقال تلقائياً
        deactivate StatusService
    end

    SigningService --> Controller: كائن التوقيع (DocumentSignature)
    deactivate SigningService
    Controller --> UI: تم التوقيع بنجاح وتحديث الحالة
    deactivate Controller
    deactivate UI
end
@enduml
```

---

## 6. مخطط التتابع: إصدار PDF والتحقق عبر QR (Sequence Diagram 5)
### عنوان المخطط: مخطط التتابع لإصدار الوثيقة والتحقق عبر QR
يوضح هذا المخطط عملية توليد الـ PDF ورمز QR وحفظ الملف في التخزين الآمن وتنزيله من قبل الخريج، والتحقق اللاحق من صحته من قبل جهة خارجية (زائر التحقق) عبر رمز التتبع أو رمز QR.

```plantuml
@startuml
title مخطط التتابع لإصدار الوثيقة والتحقق عبر QR
autonumber

actor "مدير النظام / المسؤول\n(Admin)" as Admin
boundary "لوحة التحكم\n(Admin UI)" as UI
control "RequestController" as Controller
control "DocumentIssuanceService" as IssuanceService
entity "DomPDF Engine" as DomPDF
entity "SimpleSoftwareIO QR" as QRGen
entity "Storage (Private Disk)" as Storage
entity "IssuedDocument" as DocModel
actor "الخريج\n(Graduate)" as Graduate
actor "زائر التحقق\n(Verification Visitor)" as Visitor
boundary "صفحة التحقق العامة\n(Verification Page)" as VerifyUI
control "VerificationController" as VerifyController

== توليد وإصدار الوثيقة (الـ PDF والـ QR) ==
Admin -> UI: النقر على "إصدار الوثيقة / توليد الـ PDF"
activate UI
UI -> Controller: generatePdf(documentRequest)
activate Controller
Controller -> IssuanceService: issue(documentRequest, adminId)
activate IssuanceService

note over IssuanceService: توليد رقم تسلسلي مميز\nوإنشاء رمز توكن عشوائي (qr_token)

IssuanceService -> QRGen: generate(verifyUrl)
activate QRGen
QRGen --> IssuanceService: صورة الـ QR بصيغة SVG
deactivate QRGen

IssuanceService -> DomPDF: loadView(template, data)
activate DomPDF
note over DomPDF: دمج بيانات الطالب، الدرجات،\nالرقم التسلسلي، صورة الـ QR، وصور التواقيع الإلكترونية
DomPDF --> IssuanceService: ملف PDF كـ Binary
deactivate DomPDF

IssuanceService -> Storage: put("documents/serial.pdf", pdfContent)
activate Storage
Storage --> IssuanceService: تم الحفظ بنجاح
deactivate Storage

IssuanceService -> DocModel: updateOrCreate(...)
activate DocModel
DocModel --> IssuanceService: كائن الوثيقة الصادرة (IssuedDocument)
deactivate DocModel

IssuanceService -> Controller: الوثيقة الصادرة
deactivate IssuanceService
Controller --> UI: تم توليد الـ PDF وحفظه بنجاح
deactivate Controller
UI --> Admin: عرض نجاح العملية
deactivate UI

== تحميل الوثيقة من قبل الخريج ==
Graduate -> UI: النقر على زر "تحميل الوثيقة الصادرة"
activate UI
UI -> Controller: download(documentRequest)
activate Controller
Controller -> Storage: get(pdf_path)
activate Storage
Storage --> Controller: ملف الـ PDF
deactivate Storage
Controller --> UI: تحميل الملف (attachment)
UI --> Graduate: حفظ ملف الـ PDF على الجهاز
deactivate Controller
deactivate UI

== التحقق عبر رمز الـ QR ==
Visitor -> VerifyUI: مسح رمز الـ QR بالهاتف (أو إدخال رمز التتبع يدوياً)
activate VerifyUI
VerifyUI -> VerifyController: show(token) / verify(Request)
activate VerifyController
VerifyController -> DocModel: البحث باستخدام qr_token / serial_number / tracking_code
activate DocModel
DocModel --> VerifyController: كائن الوثيقة الصادرة وتفاصيلها الأكاديمية
deactivate DocModel
VerifyController --> VerifyUI: عرض تفاصيل الطالب وصحة الوثيقة والجامعة المصدرة
deactivate VerifyController
VerifyUI --> Visitor: استعراض وثيقة الخريج المعتمدة والتحقق منها
deactivate VerifyUI
@enduml
```

---

## 7. مخطط التعاون (Collaboration Diagram)
### عنوان المخطط: مخطط التعاون بين مكونات النظام
يوضح هذا المخطط التعاون وتدفق الرسائل المرقمة بين المكونات المختلفة في النظام خلال دورة حياة طلب الوثيقة بالكامل.

```plantuml
@startuml
title مخطط التعاون بين مكونات النظام

object "الخريج\n(Graduate)" as Graduate
object "واجهة الطلبات\n(Request UI)" as UI
object "DocumentRequestController" as Controller
object "DocumentRequest\n(Model)" as RequestModel
object "المسؤول المالي\n(Finance Admin)" as Finance
object "المسؤول الأكاديمي\n(Academic Admin)" as Academic
object "DocumentIssuanceService" as Issuance
object "IssuedDocument\n(Model)" as IssuedDoc
object "الموقّعون الإلكترونيون\n(Signers)" as Signers
object "محرك الـ PDF والـ QR\n(PDF/QR Engine)" as Engines

Graduate -right-> UI : " 1: تقديم الطلب ورفع الإثبات "
UI -right-> Controller : " 2: store() "
Controller -down-> RequestModel : " 3: create(status:SUBMITTED) "
Controller -up-> Finance : " 4: إشعار بوجود إثبات دفع جديد (Notification) "
Finance -down-> Controller : " 5: approve(اعتماد الدفع وتحويل الحالة لـ UNDER_REVIEW) "
Academic -right-> Controller : " 6: updateStatus(APPROVED) "
Academic -down-> Controller : " 7: sendForSignatures(إرسال للتواقيع) "
Controller -right-> Issuance : " 8: initiateDraft() "
Issuance -down-> IssuedDoc : " 9: create(مسودة الوثيقة) "
Issuance -left-> Signers : " 10: إشعار الموقّع الأول في التسلسل "
Signers -right-> Controller : " 11: signDocument(توقيع الموقّع الحالي) "
Controller -up-> Issuance : " 12: finalizeIfComplete() [عند التوقيع الأخير] "
Issuance -right-> Engines : " 13: توليد الـ PDF والـ QR وحفظ الملف "
Issuance -up-> RequestModel : " 14: تحديث الحالة إلى ISSUED "
Controller -up-> Graduate : " 15: إشعار بجاهزية الوثيقة وتحميلها "
@enduml
```

---

## 8. مخطط الحالة لطلب الوثيقة (State Chart Diagram)
### عنوان المخطط: مخطط حالات طلب الوثيقة
يوضح هذا المخطط الحالات الفعلية التي يمر بها طلب الوثيقة في قاعدة البيانات والتحولات المسموحة والقيود المفروضة عليها في الكود الفعلي لـ `RequestStatusService`.

```plantuml
@startuml
title مخطط حالات طلب الوثيقة

[*] --> SUBMITTED : تقديم طلب جديد\n(وضع إثبات الدفع قيد المراجعة)

state "تم تقديم الطلب\n(SUBMITTED)" as SUBMITTED
state "قيد المراجعة\n(UNDER_REVIEW)" as UNDER_REVIEW
state "تمت الموافقة الأكاديمية\n(APPROVED)" as APPROVED
state "بانتظار التوقيعات\n(PENDING_SIGNATURES)" as PENDING_SIGNATURES
state "جاهزة للإصدار\n(READY)" as READY
state "تم الإصدار\n(ISSUED)" as ISSUED
state "مرفوض\n(REJECTED)" as REJECTED

SUBMITTED --> UNDER_REVIEW : اعتماد الدفع من المسؤول المالي\n(payment_status = approved)
SUBMITTED --> REJECTED : رفض الدفع من المسؤول المالي\n(مستند إثبات الدفع غير صحيح)

UNDER_REVIEW --> APPROVED : اعتماد السجل الأكاديمي والبيانات\n(من المسؤول الأكاديمي)
UNDER_REVIEW --> REJECTED : رفض البيانات الأكاديمية\n(وجود أخطاء بالبيانات)

APPROVED --> PENDING_SIGNATURES : إرسال الوثيقة للتواقيع الإلكترونية\n(توليد مسودة IssuedDocument ورقم تسلسلي)

PENDING_SIGNATURES --> ISSUED : التوقيع من الموقّع الأخير بنجاح\n(تحديث تلقائي في كود الخدمة وتوليد PDF وحفظه)
PENDING_SIGNATURES --> READY : اعتماد يدوي من المسؤول\n(إذا اكتملت التواقيع بدون تحول تلقائي)
PENDING_SIGNATURES --> REJECTED : رفض/إلغاء الطلب أثناء التوقيع

READY --> ISSUED : تسليم وتحميل الوثيقة\n(تحديث حالة الطلب وإشعار الطالب)
READY --> PENDING_SIGNATURES : إعادة إصدار لإعادة التواقيع\n(reissue() / resetForReissue())

ISSUED --> PENDING_SIGNATURES : إعادة إصدار وتعديل بيانات\n(حذف الملف وإعادة التواقيع من جديد)

REJECTED --> [*]
ISSUED --> [*]
@enduml
```

---

## 9. مخطط الأنشطة العام لطلب الوثيقة (Activity Diagram)
### عنوان المخطط: مخطط النشاط العام لطلب الوثيقة
يوضح المخطط تدفق الأنشطة ونقاط اتخاذ القرار من تسجيل دخول الخريج، مروراً بالمراجعة المالية والأكاديمية، ثم التوقيعات وإصدار المستند النهائي بصيغة PDF.

```plantuml
@startuml
title مخطط النشاط العام لطلب الوثيقة

start
:تسجيل دخول الخريج;
:اختيار نوع الوثيقة واللغة والبيانات;
if (هل السجل الأكاديمي مدخل في النظام؟) then (لا)
    :عرض رسالة خطأ (يجب مراجعة شؤون الطلاب);
    stop
else (نعم)
    :إنشاء طلب وثيقة جديد;
    if (هل الدفع مطلوب للوثيقة؟) then (نعم)
        :رفع صورة إثبات الدفع;
        :حفظ الطلب بحالة (SUBMITTED) والمالية بحالة (pending_review);
        :إرسال إشعار للمسؤول المالي;
        :مراجعة إثبات الدفع من المسؤول المالي;
        if (هل إثبات الدفع صحيح وقيمة المبلغ مطابقة؟) then (لا)
            :تحديث حالة الدفع لـ (rejected);
            :إدخال سبب الرفض وإرسال إشعار للخريج;
            :يعدل الخريج إثبات الدفع ويرفعه من جديد;
            backward:رفع صورة إثبات الدفع;
        else (نعم)
            :تحديث حالة الدفع لـ (approved);
            :تحديث حالة الطلب تلقائياً إلى (UNDER_REVIEW);
        end if
    else (لا)
        :حفظ الطلب بحالة (UNDER_REVIEW) والدفع (not_required);
    end if
    
    :مراجعة البيانات الأكاديمية (المسؤول الأكاديمي);
    if (هل البيانات الأكاديمية صحيحة؟) then (لا)
        :تحديث حالة الطلب إلى (REJECTED) وإشعار الخريج;
        stop
    else (نعم)
        :تحديث حالة الطلب إلى (APPROVED);
        :النقر على "إرسال لسير التوقيعات";
        :تحديث حالة الطلب لـ (PENDING_SIGNATURES)\nوإنشاء مسودة وتوليد الرقم التسلسلي والتوكن;
        
        repeat
            :تحديد الموقّع الحالي وإرسال إشعار (SignatureRequired) له;
            :دخول الموقّع وتدقيق الوثيقة وتوقيعها إلكترونياً;
            :حفظ التوقيع وسجل التوقيع (DocumentSignature);
        repeat while (هل يتبقى موقّعون في السلسلة؟) is (نعم) not (لا)
        
        :توليد ملف الـ PDF النهائي ودمج التواقيع إلكترونياً;
        :حفظ الملف في التخزين الآمن (Storage);
        :تحديث حالة الطلب تلقائياً إلى (ISSUED);
        :إرسال إشعار للخريج بصدور الوثيقة;
        :دخول الخريج وتحميل الوثيقة PDF;
        :التحقق اللاحق من الوثيقة عبر مسح رمز الـ QR والتوكن;
    end if
end if
stop
@enduml
```

---

## 10. مخطط الأنشطة بالمسارات (Swimlanes Activity Diagram)
### عنوان المخطط: مخطط النشاط بالمسارات لنظام بوابة خدمات الخريجين
يوضح هذا المخطط توزيع المهام والمسؤوليات بين كافة الفاعلين في النظام (الخريج، النظام، المسؤول المالي، المسؤول الأكاديمي، الموقّعون، مدير النظام، جهة التحقق).

```plantuml
@startuml
title مخطط النشاط بالمسارات لنظام بوابة خدمات الخريجين

|الخريج|
start
:تسجيل الدخول وطلب وثيقة جديدة;
if (هل تتطلب دفعاً؟) then (نعم)
    :رفع إثبات الدفع المالي;
|النظام|
    :حفظ كـ (SUBMITTED);\n:إرسال إشعار للمالية;
|المسؤول المالي|
    :مراجعة إثبات الدفع والاعتماد;
|النظام|
    :تحديث الدفع لـ (approved) والحالة لـ (UNDER_REVIEW);\n:إرسال إشعار للخريج;
else (لا)
|النظام|
    :حفظ الطلب كـ (UNDER_REVIEW);
end if

|المسؤول الأكاديمي|
:تدقيق السجل الأكاديمي ومطابقته;
:اعتماد المراجعة الأكاديمية (APPROVED);
:النقر على "إرسال لسير التوقيعات";

|النظام|
:تحديث حالة الطلب إلى (PENDING_SIGNATURES);\n:توليد التوكن والرقم التسلسلي;\n:إرسال إشعار للموقّع الأول;

|الموقّعون|
repeat
    :مراجعة تفاصيل المستند والتوقيع إلكترونياً;
|النظام|
    :تسجيل التوقيع والتحقق من الترتيب التسلسلي;
backward:إرسال إشعار للموقّع التالي;
repeat while (هل توجد تواقيع متبقية؟) is (نعم) not (لا)

|النظام|
:توليد الـ PDF النهائي متضمناً صور التواقيع والـ QR;\n:تحديث حالة الطلب تلقائياً لـ (ISSUED);\n:إرسال إشعار للخريج;

|الخريج|
:تحميل الوثيقة الصادرة PDF;
:تقديم الوثيقة للجهات الخارجية;

|جهة التحقق|
:مسح رمز الـ QR المطبوع على الوثيقة;

|النظام|
:التحقق من التوكن وصحة الوثيقة في قاعدة البيانات;

|جهة التحقق|
:استعراض تفاصيل الوثيقة الرسمية المؤكدة;
stop
@enduml
```

---

## 11. مخطط الطبقات (Layer Diagram)
### عنوان المخطط: مخطط طبقات نظام بوابة خدمات الخريجين
يوضح هذا المخطط بنية النظام وهيكلته الموزعة على طبقات Laravel المختلفة لضمان الفصل التام بين منطق العمل والواجهات وقواعد البيانات.

```plantuml
@startuml
title مخطط طبقات نظام بوابة خدمات الخريجين
left to right direction

package "طبقة العرض (Presentation Layer)" {
    [Blade Views (Arabic/RTL)] as views
    [Bootstrap 5 / CSS] as css
    [Alpine.js / JavaScript] as js
    [Flatpickr (Gregorian Picker)] as flatpickr
    [FontAwesome Icons] as icons
}

package "طبقة التحكم والتوجيه (Control Layer)" {
    [Web Routes (web.php)] as routes
    [Middlewares (role, permission, throttle)] as middleware
    [AuthController] as auth_ctrl
    [DocumentRequestController] as req_ctrl
    [VerificationController] as verify_ctrl
    [Admin Controllers] as admin_ctrl
    [Employer Controllers] as employer_ctrl
}

package "طبقة منطق الأعمال (Business Logic Layer)" {
    [DocumentSigningService] as sign_srv
    [DocumentIssuanceService] as issue_srv
    [RequestStatusService] as status_srv
    [StudentInformationService] as student_srv
    [StudentInfo Drivers (Api, Database, Excel)] as drivers
}

package "طبقة النماذج (Model/Eloquent Layer)" {
    [User Model] as user_m
    [Graduate Model] as grad_m
    [DocumentRequest Model] as req_m
    [IssuedDocument Model] as doc_m
    [DocumentSignature Model] as sig_m
    [AcademicRecord Models (Levels, Semesters, Subjects)] as academic_m
    [GradesCertificate Models] as grades_m
    [Job / Application Models] as job_m
}

package "طبقة قاعدة البيانات والتخزين (Database & Storage Layer)" {
    database "SQLite Database" as sqlite
    [Private Storage (local disk)] as private_store
    [Public Storage (public disk)] as public_store
}

package "الخدمات المساندة والمكتبات (Supporting Services)" {
    [DomPDF Engine] as dompdf
    [SimpleSoftwareIO QR Generator] as qr_gen
    [Laravel Notifications System] as notif_sys
    [Maatwebsite Excel Import/Export] as excel_sys
}

' العلاقات والربط بين الطبقات
views ..> routes : "طلب الصفحة"
routes ..> middleware : "المرور بالفلاتر"
middleware ..> admin_ctrl : "توجيه الطلب"
middleware ..> req_ctrl
middleware ..> auth_ctrl

admin_ctrl ..> sign_srv : "استدعاء منطق العمل"
admin_ctrl ..> issue_srv
req_ctrl ..> issue_srv
verify_ctrl ..> doc_m

sign_srv ..> status_srv
issue_srv ..> status_srv
issue_srv ..> student_srv
student_srv ..> drivers

sign_srv ..> user_m
sign_srv ..> sig_m
issue_srv ..> doc_m
drivers ..> academic_m
drivers ..> grades_m

user_m ..> sqlite : "Eloquent ORM"
grad_m ..> sqlite
req_m ..> sqlite
doc_m ..> sqlite

req_ctrl ..> private_store : "حفظ إثباتات الدفع"
issue_srv ..> private_store : "حفظ ملفات الـ PDF"
issue_srv ..> dompdf : "استخدام لتوليد الـ PDF"
issue_srv ..> qr_gen : "استخدام لتوليد رمز الـ QR"
status_srv ..> notif_sys : "إرسال الإشعارات للخريج"
sign_srv ..> notif_sys : "إرسال الإشعارات للموقّعين"

@enduml
```

---

## 12. مخطط العلاقات والكيانات (ERD Diagram)
### عنوان المخطط: مخطط الكيانات والعلاقات لقاعدة البيانات
يوضح هذا المخطط البنية الفعلية لجداول قاعدة البيانات والعلاقات بينها بالاعتماد على الهجرات (Migrations) المنفذة في الكود الفعلي للنظام.

```plantuml
@startuml
title مخطط الكيانات والعلاقات لقاعدة البيانات
hide circle
skinparam linetype ortho

entity "users\nالمستخدمون" as users {
    * id : INTEGER <<PK>>
    --
    name : VARCHAR
    email : VARCHAR <<Unique>>
    password : VARCHAR
    role : VARCHAR
    is_active : BOOLEAN
    signature_image : VARCHAR
    signer_role : VARCHAR
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "graduates\nالخريجون" as graduates {
    * user_id : INTEGER <<PK>> <<FK>>
    --
    university_id : VARCHAR <<Unique>>
    phone : VARCHAR
    major_id : INTEGER <<FK>>
    graduation_year : YEAR
    photo : VARCHAR
    cv_path : VARCHAR
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "approved_graduates\nالخريجون المعتمدون" as approved_graduates {
    * id : INTEGER <<PK>>
    --
    university_id : VARCHAR <<Unique>>
    name : VARCHAR
    email : VARCHAR
    major : VARCHAR
    college : VARCHAR
    graduation_year : INTEGER
    is_frozen : BOOLEAN
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "faculties\nالكليات" as faculties {
    * id : INTEGER <<PK>>
    --
    name_ar : VARCHAR
    name_en : VARCHAR
    description : TEXT
    status : VARCHAR
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "majors\nالتخصصات" as majors {
    * id : INTEGER <<PK>>
    --
    faculty_id : INTEGER <<FK>>
    name_ar : VARCHAR
    name_en : VARCHAR
    degree_ar : VARCHAR
    degree_en : VARCHAR
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "document_types\nأنواع الوثائق" as document_types {
    * id : INTEGER <<PK>>
    --
    name_ar : VARCHAR
    name_en : VARCHAR
    code : VARCHAR <<Unique>>
    fee_amount : DECIMAL(10,2)
    currency : VARCHAR(3)
    payment_required : BOOLEAN
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "document_requests\nطلبات الوثائق" as document_requests {
    * id : INTEGER <<PK>>
    --
    user_id : INTEGER <<FK>>
    document_type_id : INTEGER <<FK>>
    tracking_code : VARCHAR <<Unique>>
    language : ENUM('AR', 'EN')
    purpose : VARCHAR(255)
    delivery_type : ENUM('DIGITAL_PDF', 'PICKUP')
    status : ENUM('SUBMITTED', 'UNDER_REVIEW', 'APPROVED', 'REJECTED', 'READY', 'ISSUED')
    admin_note : TEXT
    payment_status : VARCHAR(20)
    payment_proof_path : VARCHAR
    payment_reviewed_by : INTEGER <<FK>>
    payment_reviewed_at : TIMESTAMP
    payment_rejection_reason : TEXT
    fee_amount : DECIMAL(10,2)
    currency : VARCHAR(3)
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "issued_documents\nالوثائق الصادرة" as issued_documents {
    * id : INTEGER <<PK>>
    --
    document_request_id : INTEGER <<FK>> <<Unique>>
    serial_number : VARCHAR <<Unique>>
    qr_token : VARCHAR <<Unique>>
    pdf_path : VARCHAR
    issued_at : TIMESTAMP
    is_valid : BOOLEAN
    revoked_at : TIMESTAMP
    all_signed_at : TIMESTAMP
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "document_signatures\nالتواقيع الإلكترونية" as document_signatures {
    * id : INTEGER <<PK>>
    --
    issued_document_id : INTEGER <<FK>>
    user_id : INTEGER <<FK>>
    role_title : VARCHAR
    signed_at : TIMESTAMP
    ip_address : VARCHAR(45)
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "request_status_logs\nسجل حركات الطلب" as request_status_logs {
    * id : INTEGER <<PK>>
    --
    document_request_id : INTEGER <<FK>>
    admin_id : INTEGER <<FK>>
    from_status : VARCHAR
    to_status : VARCHAR
    note : TEXT
    created_at : TIMESTAMP
}

entity "graduate_academic_records\nالسجلات الأكاديمية العامة" as graduate_academic_records {
    * id : INTEGER <<PK>>
    --
    user_id : INTEGER <<FK>> <<Unique>>
    student_name_ar : VARCHAR
    student_name_en : VARCHAR
    university_number : VARCHAR(64)
    degree_ar : VARCHAR
    degree_en : VARCHAR
    total_marks : VARCHAR(32)
    gpa : VARCHAR(32)
    overall_rating : VARCHAR(64)
    honors_rank : VARCHAR(128)
    graduation_year_label : VARCHAR(64)
    enrollment_year_label : VARCHAR(64)
    exam_session : VARCHAR(64)
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "graduate_academic_levels\nالمستويات الدراسية للسجل" as graduate_academic_levels {
    * id : INTEGER <<PK>>
    --
    graduate_academic_record_id : INTEGER <<FK>>
    sort_order : INTEGER
    name : VARCHAR(64)
    academic_year : VARCHAR(64)
    level_avg : VARCHAR(32)
    final_result : VARCHAR(64)
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "graduate_academic_semesters\nالفصول الدراسية للسجل" as graduate_academic_semesters {
    * id : INTEGER <<PK>>
    --
    graduate_academic_level_id : INTEGER <<FK>>
    sort_order : INTEGER
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "graduate_academic_subjects\nالمواد الدراسية للسجل" as graduate_academic_subjects {
    * id : INTEGER <<PK>>
    --
    graduate_academic_semester_id : INTEGER <<FK>>
    sort_order : INTEGER
    catalog_key : VARCHAR(64)
    name : VARCHAR(512)
    credit_hours : VARCHAR(16)
    score : VARCHAR(32)
    rating : VARCHAR(64)
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "grades_certificates\nبيانات شهادات الدرجات" as grades_certificates {
    * id : INTEGER <<PK>>
    --
    user_id : INTEGER <<FK>> <<Unique>>
    student_name_ar : VARCHAR
    student_name_en : VARCHAR
    university_number : VARCHAR(64)
    degree_ar : VARCHAR
    degree_en : VARCHAR
    total_marks : VARCHAR(32)
    gpa : VARCHAR(32)
    overall_rating : VARCHAR(64)
    honors_rank : VARCHAR(128)
    graduation_year_label : VARCHAR(64)
    enrollment_year_label : VARCHAR(64)
    exam_session : VARCHAR(64)
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "grades_certificate_levels\nمستويات شهادة الدرجات" as grades_certificate_levels {
    * id : INTEGER <<PK>>
    --
    grades_certificate_id : INTEGER <<FK>>
    sort_order : INTEGER
    name : VARCHAR(64)
    academic_year : VARCHAR(64)
    level_avg : VARCHAR(32)
    final_result : VARCHAR(64)
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "grades_certificate_semesters\nفصول شهادة الدرجات" as grades_certificate_semesters {
    * id : INTEGER <<PK>>
    --
    grades_certificate_level_id : INTEGER <<FK>>
    sort_order : INTEGER
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "grades_certificate_subjects\nمواد شهادة الدرجات" as grades_certificate_subjects {
    * id : INTEGER <<PK>>
    --
    grades_certificate_semester_id : INTEGER <<FK>>
    sort_order : INTEGER
    catalog_key : VARCHAR(64)
    name : VARCHAR(512)
    credit_hours : VARCHAR(16)
    score : VARCHAR(32)
    rating : VARCHAR(64)
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "portal_jobs\nفرص العمل للتوظيف" as portal_jobs {
    * id : INTEGER <<PK>>
    --
    employer_id : INTEGER <<FK>>
    title : VARCHAR
    description : TEXT
    requirements : TEXT
    deadline : DATE
    location : VARCHAR
    job_type : VARCHAR
    status : VARCHAR
    rejection_reason : TEXT
    closed_at : TIMESTAMP
    is_filled : BOOLEAN
    filled_at : TIMESTAMP
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "job_applications\nطلبات التوظيف" as job_applications {
    * id : INTEGER <<PK>>
    --
    job_id : INTEGER <<FK>>
    graduate_id : INTEGER <<FK>>
    cover_letter : TEXT
    cv_path : VARCHAR
    status : VARCHAR
    employer_notes : TEXT
    interview_date : TIMESTAMP
    interview_notes : TEXT
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

entity "employers\nأصحاب العمل" as employers {
    * user_id : INTEGER <<PK>> <<FK>>
    --
    company_name : VARCHAR
    phone : VARCHAR
    status : VARCHAR
    industry : VARCHAR
    description : TEXT
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

' العلاقات وتحديد أطرافها ورؤوسها
users ||--o| graduates : "يمتلك تفاصيل خريج"
users ||--o| employers : "يمتلك تفاصيل شركة"
faculties ||--o{ majors : "تحتوي على"
majors ||--o{ graduates : "ينتمي إليها الخريج"

users ||--o{ document_requests : "يقدم الطلبات"
document_types ||--o{ document_requests : "يحدد نوع الطلب"
users ||--o{ document_requests : "يراجع الدفعة المالية (payment_reviewed_by)"

document_requests ||--o| issued_documents : "تنتج وثيقة صادرة"
issued_documents ||--o{ document_signatures : "تتطلب تواقيع"
users ||--o{ document_signatures : "يوقّع الموقّع"

document_requests ||--o{ request_status_logs : "تاريخ حركات الطلب"
users ||--o{ request_status_logs : "يغير الحالة (admin_id)"

users ||--o| graduate_academic_records : "سجله الأكاديمي"
graduate_academic_records ||--o{ graduate_academic_levels : "يحتوي مستويات"
graduate_academic_levels ||--o{ graduate_academic_semesters : "يحتوي فصولاً"
graduate_academic_semesters ||--o{ graduate_academic_subjects : "يحتوي مواداً"

users ||--o| grades_certificates : "بيان درجاته"
grades_certificates ||--o{ grades_certificate_levels : "يحتوي مستويات"
grades_certificate_levels ||--o{ grades_certificate_semesters : "يحتوي فصولاً"
grades_certificate_semesters ||--o{ grades_certificate_subjects : "يحتوي مواداً"

users ||--o{ portal_jobs : "ينشر الوظائف (employer_id)"
portal_jobs ||--o{ job_applications : "يستقبل التقديمات"
users ||--o{ job_applications : "يقدم على وظيفة (graduate_id)"

@enduml
```

---

## 13. مخطط المكونات (Component Diagram)
### عنوان المخطط: مخطط المكونات لنظام بوابة خدمات الخريجين
يوضح هذا المخطط المكونات المادية لنظام بوابة الخريجين والربط بين واجهات العرض والمحركات الخدمية مثل توليد الـ PDF ورمز QR وقاعدة البيانات.

```plantuml
@startuml
title مخطط المكونات لنظام بوابة خدمات الخريجين

skinparam componentStyle uml2

package "الواجهة الأمامية\n(Frontend Subsystem)" {
    [واجهات الويب والـ Blade]\n(Blade Views) as views
    [تنسيقات التصميم والتحكم الأمامي]\n(CSS & Alpine JS) as styles
}

package "التحكم والأمن\n(Controller & Routing Subsystem)" {
    [متحكم طلبات الوثائق]\n(DocumentRequestController) as req_ctrl
    [متحكم التوقيع الإلكتروني]\n(SignatureController) as sign_ctrl
    [متحكم مراجعة الدفع]\n(PaymentReviewController) as pay_ctrl
    [متحكم التحقق العام]\n(VerificationController) as verify_ctrl
}

package "منطق الأعمال والخدمات\n(Business Logic Services)" {
    [خدمة إصدار الوثائق]\n(DocumentIssuanceService) as issue_srv
    [خدمة التواقيع الإلكترونية]\n(DocumentSigningService) as sign_srv
    [خدمة تتبع وإدارة الحالة]\n(RequestStatusService) as status_srv
    [مزود البيانات الأكاديمية للطلاب]\n(StudentInformationProvider) as student_srv
}

package "النماذج والبيانات\n(Data Subsystem)" {
    [نماذج Eloquent ORM]\n(Models) as models
    database "قاعدة بيانات SQLite"\n(SQLite DB) as db
    [نظام التخزين الخاص والعام]\n(Storage Engine) as storage
}

package "المكونات الخارجية والمكتبات\n(Third-party Engines)" {
    [توليد الـ PDF]\n(DomPDF Library) as dompdf
    [توليد الـ QR Code]\n(SimpleSoftwareIO QR) as qr_gen
    [نظام الإشعارات للبريد واللوحة]\n(Laravel Notification System) as notif
}

' روابط المكونات
views -down-> req_ctrl : "تقديم الطلب ورفع الدفع"
views -down-> sign_ctrl : "توقيع الوثيقة"
views -down-> pay_ctrl : "اعتماد الدفع"
views -down-> verify_ctrl : "التحقق من الوثيقة"

req_ctrl .right.> styles
req_ctrl -down-> issue_srv
req_ctrl -down-> notif
sign_ctrl -down-> sign_srv
pay_ctrl -down-> status_srv
pay_ctrl -down-> notif
verify_ctrl -down-> models

issue_srv -down-> status_srv
issue_srv -down-> student_srv
issue_srv -down-> dompdf
issue_srv -down-> qr_gen
issue_srv -down-> storage

sign_srv -down-> status_srv
sign_srv -down-> notif
status_srv -down-> notif
status_srv -down-> models

models -down-> db : "قراءة/كتابة"
storage -down-> db : "حفظ مسارات الملفات"
@enduml
```

---

## 14. مخطط النشر (Deployment Diagram)
### عنوان المخطط: مخطط النشر لنظام بوابة خدمات الخريجين
يوضح مخطط النشر هذا كيفية توزيع وتشغيل النظام على الأجهزة الفعلية للمخدمات وعقدة العميل والتفاعل مع قواعد البيانات ومخزن الملفات الخاص.

```plantuml
@startuml
title مخطط النشر لنظام بوابة خدمات الخريجين

node "جهاز المستخدم / المتصفح\n(Client Device / Browser)" as client {
    [واجهة البوابة والخريج\n(HTML5, CSS, Alpine.js)] as web_app
}

node "خادم تطبيق Laravel\n(Laravel Application Server)" as server {
    node "خادم الويب\n(Apache / Nginx)" as web_server {
        [مكونات البوابة الخلفية\n(Laravel 11, PHP 8.2)] as laravel_app
        [محرك توليد المستندات\n(DomPDF Engine)] as pdf_engine
        [محرك توليد الـ QR\n(SimpleSoftwareIO QR)] as qr_engine
    }
    
    node "نظام الملفات\n(Local Storage Disk)" as local_disk {
        folder "payment-proofs/" as proofs_dir
        folder "documents/" as pdf_dir
        folder "signatures/" as sig_dir
    }
}

node "خادم قاعدة البيانات\n(Database Node)" as db_node {
    database "SQLite Database File\n(database.sqlite)" as db_file
}

' روابط الاتصال والتوزيع
client -- server : "بروتوكول HTTPS / منفذ 443"
web_app <--> laravel_app : "طلبات HTTP والردود"

laravel_app --> pdf_engine : "استدعاء لتوليد التقرير"
laravel_app --> qr_engine : "توليد رمز QR للتوكن"

laravel_app --> local_disk : "حفظ/استرجاع الملفات"
pdf_engine --> pdf_dir : "تخزين مستندات PDF الصادرة"
laravel_app --> proofs_dir : "تخزين إثباتات دفع الخريجين"
laravel_app --> sig_dir : "تخزين صور التواقيع الإلكترونية"

laravel_app -right-> db_node : "قراءة/كتابة عبر Eloquent"
db_file -[hidden]- db_node
@enduml
```
