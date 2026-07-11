# مخططات Mermaid لبوابة خدمات الخريجين
**Graduation Services Portal - Saba Region University**

يحتوي هذا الملف على كافة مخططات UML الخاصة بالنظام الفعلي لبوابة خدمات الخريجين بجامعة إقليم سبأ، مكتوبة بلغة Mermaid ومجهزة للعرض المباشر في GitHub أو المواقع الداعمة لـ Mermaid مثل [Mermaid Live Editor](https://mermaid.live/).

---

## 1. مخطط حالات الاستخدام (Use Case Diagram)
### عنوان المخطط: مخطط حالات الاستخدام لنظام بوابة خدمات الخريجين
يوضح هذا المخطط الفاعلين الفعليين في النظام وحالات الاستخدام الخاصة بكل منهم مع علاقات التضمين والتمديد.

```mermaid
flowchart LR
    %% Actors
    subgraph Actors [الفاعلون]
        graduate["الخريج (Graduate)"]
        finance["المسؤول المالي (Finance Admin)"]
        academic["المسؤول الأكاديمي (Academic Admin)"]
        signer["الموقّع الإلكتروني (Signer)"]
        super["مدير النظام (Super Admin)"]
        employer["جهة التوظيف (Employer)"]
        employment["مسؤول التوظيف (Employment Officer)"]
        verifier["زائر التحقق (Verifier)"]
    end

    %% System Boundary
    subgraph System [بوابة خدمات الخريجين]
        UC_login["تسجيل الدخول (Login)"]
        UC_register_grad["تسجيل خريج جديد (Register Graduate)"]
        UC_request_doc["تقديم طلب وثيقة (Submit Request)"]
        UC_upload_payment["رفع إثبات الدفع (Upload Payment)"]
        UC_track_request["متابعة حالة الطلب (Track Status)"]
        UC_review_payment["مراجعة إثبات الدفع (Review Payment)"]
        UC_academic_review["المراجعة الأكاديمية (Academic Review)"]
        UC_manage_record["إدارة السجل الأكاديمي (Manage Record)"]
        UC_send_signatures["إرسال الوثيقة للتوقيعات (Send for Signatures)"]
        UC_sign_sequentially["التوقيع الإلكتروني التسلسلي (Sequential Signing)"]
        UC_generate_pdf["إصدار الوثيقة والـ PDF (Generate PDF & Issue)"]
        UC_download_pdf["تحميل الوثيقة (Download Document)"]
        UC_verify_qr["التحقق عبر QR (Verify via QR)"]
        UC_manage_jobs["إدارة فرص العمل (Manage Job Offers)"]
        UC_apply_job["التقديم على فرصة عمل (Apply for Job)"]
        UC_manage_employers["إدارة أصحاب العمل (Manage Employers)"]
        UC_manage_users["إدارة المستخدمين والصلاحيات (Manage Users)"]
    end

    %% Links
    graduate --> UC_login
    graduate --> UC_register_grad
    graduate --> UC_request_doc
    graduate --> UC_upload_payment
    graduate --> UC_track_request
    graduate --> UC_download_pdf
    graduate --> UC_apply_job

    finance --> UC_login
    finance --> UC_review_payment

    academic --> UC_login
    academic --> UC_academic_review
    academic --> UC_manage_record
    academic --> UC_send_signatures

    signer --> UC_login
    signer --> UC_sign_sequentially

    super --> UC_login
    super --> UC_manage_users
    super --> UC_generate_pdf

    employer --> UC_login
    employer --> UC_manage_jobs

    employment --> UC_login
    employment --> UC_manage_employers

    verifier --> UC_verify_qr

    UC_request_doc -.->|تشمل/امتداد| UC_upload_payment
    UC_send_signatures -.->|تشمل| UC_sign_sequentially
    UC_sign_sequentially -.->|تشمل| UC_generate_pdf
    UC_academic_review -.->|تشمل| UC_manage_record
```

---

## 2. مخطط التتابع: تقديم طلب وثيقة ورفع إثبات الدفع (Sequence Diagram 1)
### عنوان المخطط: مخطط التتابع لتقديم طلب وثيقة ورفع إثبات الدفع

```mermaid
sequenceDiagram
    autonumber
    actor Graduate as الخريج (Graduate)
    participant UI as واجهة الخريج (Graduate UI)
    participant Controller as DocumentRequestController
    participant Provider as StudentInformationProvider
    participant Storage as التخزين (Private Disk)
    participant DB as قاعدة البيانات (Database)
    participant Notif as نظام الإشعارات (Notification System)
    actor Finance as المسؤول المالي (Finance Admin)

    Graduate->>UI: اختيار نوع الوثيقة واللغة وإدخال البيانات
    activate UI
    UI->>Controller: store(Request)
    activate Controller

    Controller->>Provider: hasAcademicRecord(User)
    activate Provider
    Provider-->>Controller: true / false
    deactivate Provider

    alt السجل الأكاديمي غير متوفر
        Controller-->>UI: خطأ: السجل الأكاديمي غير مدخل للخرج
        UI-->>Graduate: عرض رسالة الخطأ
    else السجل الأكاديمي متوفر
        alt الدفع مطلوب للوثيقة
            Controller->>Storage: تخزين ملف إثبات الدفع (payment-proofs/)
            activate Storage
            Storage-->>Controller: payment_proof_path
            deactivate Storage
        end

        Controller->>DB: حفظ الطلب (status: SUBMITTED, payment_status: pending_review)
        activate DB
        DB-->>Controller: تم حفظ سجل الطلب بنجاح
        deactivate DB

        alt الدفع مطلوب ومرفق
            Controller->>Notif: إرسال إشعار (NewPaymentProofSubmitted)
            activate Notif
            Notif->>Finance: إشعار المسؤولين الماليين النشطين فقط
            Notif-->>Controller: تم الإرسال
            deactivate Notif
        end

        Controller-->>UI: إعادة توجيه مع رسالة نجاح
        deactivate Controller
        UI-->>Graduate: عرض تأكيد تقديم الطلب بنجاح
        deactivate UI
    end
```

---

## 3. مخطط التتابع: مراجعة المالية (Sequence Diagram 2)
### عنوان المخطط: مخطط التتابع لمراجعة إثبات الدفع

```mermaid
sequenceDiagram
    autonumber
    actor Finance as المسؤول المالي (Finance Admin)
    participant UI as لوحة المالية (Finance Dashboard)
    participant Controller as PaymentReviewController
    participant Storage as التخزين (Private Disk)
    participant DB as قاعدة البيانات (Database)
    participant Notif as نظام الإشعارات (Notification System)
    actor Graduate as الخريج (Graduate)

    Finance->>UI: فتح تفاصيل دفعات الطلبات المعلقة
    activate UI
    UI->>Controller: showProof(documentRequest)
    activate Controller
    Controller->>Storage: استرجاع ملف إثبات الدفع
    activate Storage
    Storage-->>Controller: محتوى الملف (Stream)
    deactivate Storage
    Controller-->>UI: عرض الملف بأمان (Download Stream)
    UI-->>Finance: استعراض صورة/ملف إثبات الدفع
    deactivate Controller

    alt اعتماد الدفع (Approve)
        Finance->>UI: النقر على "اعتماد الدفع"
        UI->>Controller: approve(documentRequest)
        activate Controller
        Controller->>DB: تحديث (payment_status: approved, status: UNDER_REVIEW) وكتابة Log
        activate DB
        DB-->>Controller: تم التحديث بنجاح
        deactivate DB
        Controller->>Notif: إرسال إشعار (PaymentProofApproved)
        activate Notif
        Notif->>Graduate: إشعار الخريج باعتماد الدفع وتحول الطلب للمراجعة الأكاديمية
        Notif-->>Controller: تم الإرسال
        deactivate Notif
        Controller-->>UI: رسالة نجاح الاعتماد
        deactivate Controller
        UI-->>Finance: تحديث الواجهة إلى معتمد
    else رفض الدفع (Reject)
        Finance->>UI: إدخال سبب الرفض والنقر على "رفض"
        UI->>Controller: reject(Request, documentRequest)
        activate Controller
        Controller->>DB: تحديث (payment_status: rejected, save reason)
        activate DB
        DB-->>Controller: تم التحديث بنجاح
        deactivate DB
        Controller->>Notif: إرسال إشعار (PaymentProofRejected)
        activate Notif
        Notif->>Graduate: إشعار الخريج برفض الدفع مع توضيح السبب
        Notif-->>Controller: تم الإرسال
        deactivate Notif
        Controller-->>UI: رسالة نجاح الرفض
        deactivate Controller
        UI-->>Finance: تحديث الواجهة إلى مرفوض
        deactivate UI
    end
```

---

## 4. مخطط التتابع: المراجعة الأكاديمية وتجهيز الوثيقة (Sequence Diagram 3)
### عنوان المخطط: مخطط التتابع للمراجعة الأكاديمية وتجهيز الوثيقة

```mermaid
sequenceDiagram
    autonumber
    actor Academic as المسؤول الأكاديمي (Academic Admin)
    participant UI as لوحة المراجعة الأكاديمية (Academic Dashboard)
    participant Controller as RequestController
    participant Provider as StudentInformationProvider
    participant Issuance as DocumentIssuanceService
    participant StatusService as RequestStatusService
    participant Signing as DocumentSigningService
    participant DB as قاعدة البيانات (Database)
    participant Notif as نظام الإشعارات (Notification System)
    actor Signer as الموقّع الأول (First Signer)

    Academic->>UI: فتح تفاصيل طلب الوثيقة
    activate UI
    UI->>Controller: show(documentRequest)
    activate Controller
    Controller->>Provider: hasAcademicRecord(User)
    activate Provider
    Provider-->>Controller: true
    deactivate Provider
    Controller-->>UI: عرض تفاصيل الطلب وبياناته الأكاديمية
    deactivate Controller

    Academic->>UI: اعتماد المراجعة الأكاديمية وتحديث الحالة إلى APPROVED
    UI->>Controller: updateStatus(Request, documentRequest)
    activate Controller
    Controller->>StatusService: transition(documentRequest, APPROVED, ...)
    activate StatusService
    StatusService->>DB: تحديث الحالة إلى APPROVED وكتابة Log
    activate DB
    DB-->>StatusService: تم التحديث
    deactivate DB
    StatusService->>Notif: إشعار الخريج بالموافقة الأكاديمية (APPROVED)
    activate Notif
    Notif->>Academic: إرجاع النجاح
    deactivate Notif
    StatusService-->>Controller: نجاح الانتقال
    deactivate StatusService
    Controller-->>UI: تم التحديث بنجاح
    deactivate Controller

    Academic->>UI: النقر على "إرسال لسير التوقيعات"
    UI->>Controller: sendForSignatures(documentRequest)
    activate Controller
    Controller->>Issuance: initiateDraft(documentRequest, adminId)
    activate Issuance
    Issuance->>DB: إنشاء سجل مسودة الوثيقة (IssuedDocument)\nوتوليد الرقم التسلسلي ورمز QR والتوكن
    activate DB
    DB-->>Issuance: مسودة الوثيقة (IssuedDocument)
    deactivate DB
    Issuance->>StatusService: transition(documentRequest, PENDING_SIGNATURES, ...)
    activate StatusService
    StatusService->>DB: تحديث حالة الطلب إلى PENDING_SIGNATURES
    activate DB
    DB-->>StatusService: تم التحديث
    deactivate DB
    StatusService-->>Issuance: نجاح الانتقال
    deactivate StatusService
    Issuance->>Signing: notifyCurrentSigner(IssuedDocument)
    activate Signing
    Signing->>DB: البحث عن الموقّع الأول الفعال في التسلسل
    activate DB
    DB-->>Signing: كائن الموقّع الأول (User)
    deactivate DB
    Signing->>Notif: إرسال إشعار التوقيع المطلوب (SignatureRequired)
    activate Notif
    Notif->>Signer: تنبيه بالبريد/اللوحة بوجود وثيقة تتطلب توقيعه
    Notif-->>Signing: تم الإرسال
    deactivate Notif
    Signing-->>Issuance: تم الإشعار
    deactivate Signing
    Issuance-->>Controller: كائن الوثيقة الصادرة المسودة
    deactivate Issuance
    Controller-->>UI: تم إرسال الوثيقة لسير التوقيعات بنجاح
    deactivate Controller
    deactivate UI
```

---

## 5. مخطط التتابع: التوقيع الإلكتروني التسلسلي (Sequence Diagram 4)
### عنوان المخطط: مخطط التتابع للتوقيع الإلكتروني التسلسلي

```mermaid
sequenceDiagram
    autonumber
    actor Signer as الموقّع الحالي (Current Signer)
    participant UI as واجهة التوقيعات المعلقة (Signatures UI)
    participant Controller as SignatureController
    participant SigningService as DocumentSigningService
    participant DocModel as IssuedDocument (Model)
    participant DB as قاعدة البيانات (Database)
    participant IssuanceService as DocumentIssuanceService
    participant StatusService as RequestStatusService
    participant Notif as نظام الإشعارات (Notification System)
    actor NextSigner as الموقّع التالي (Next Signer)
    actor Graduate as الخريج (Graduate)

    Signer->>UI: استعراض الوثائق بانتظار توقيعي
    activate UI
    UI->>Controller: pendingSignatures(Request)
    activate Controller
    Controller->>SigningService: getPendingForUser(User)
    activate SigningService
    SigningService->>DocModel: getCurrentSigner()
    activate DocModel
    DocModel-->>SigningService: الموقّع الحالي المطلوب
    deactivate DocModel
    SigningService-->>Controller: قائمة الوثائق الخاصة بدور المستخدم الحالي
    deactivate SigningService
    Controller-->>UI: عرض جدول الوثائق المعلقة للتوقيع
    deactivate Controller

    Signer->>UI: النقر على زر "توقيع المستند"
    UI->>Controller: signDocument(Request, issuedDocument)
    activate Controller
    Controller->>SigningService: sign(User, issuedDocument, roleTitle, ip)
    activate SigningService

    alt غير مصرح له أو ليس دوره في التسلسل الحالي
        SigningService-->>Controller: استثناء: ليس دورك الحالي للتوقيع
        Controller-->>UI: عرض رسالة خطأ
    else مصرح له وهو دوره في التسلسل
        SigningService->>DB: transaction { create DocumentSignature }
        activate DB
        DB-->>SigningService: تم الحفظ والتوقيع
        deactivate DB
        
        SigningService->>SigningService: finalizeIfComplete(issuedDocument)
        activate SigningService
        
        alt يتبقى موقّعون آخرون في السلسلة
            SigningService-->>SigningService: لا يتم توليد الـ PDF بعد
            deactivate SigningService
            SigningService->>SigningService: notifyCurrentSigner(issuedDocument)
            activate SigningService
            SigningService->>DB: البحث عن المستخدمين بالدور التالي
            activate DB
            DB-->>SigningService: الموقّعون التاليون (Users)
            deactivate DB
            SigningService->>Notif: إرسال إشعار التوقيع المطلوب (SignatureRequired)
            activate Notif
            Notif->>NextSigner: إشعار الموقّع التالي بوجود طلب يتطلب توقيعه
            Notif-->>SigningService: تم
            deactivate Notif
            deactivate SigningService
        else اكتملت جميع التوقيعات (التوقيع الأخير)
            SigningService->>IssuanceService: finalizePdf(issuedDocument)
            activate IssuanceService
            IssuanceService->>DB: تحديث مسار الـ PDF وتاريخ الإصدار وتعيين all_signed_at
            activate DB
            DB-->>IssuanceService: تم التحديث
            deactivate DB
            IssuanceService-->>SigningService: تم توليد وحفظ الملف
            deactivate IssuanceService
            deactivate SigningService
            
            SigningService->>StatusService: transition(documentRequest, ISSUED, ...)
            activate StatusService
            StatusService->>DB: تحديث حالة الطلب إلى ISSUED
            activate DB
            DB-->>StatusService: تم التحديث
            deactivate DB
            StatusService->>Notif: إرسال إشعار الخريج بجاهزية الوثيقة (ISSUED)
            activate Notif
            Notif->>Graduate: إرسال إشعار تم إصدار الوثيقة
            Notif-->>StatusService: تم
            deactivate Notif
            StatusService-->>SigningService: تم الانتقال تلقائياً
            deactivate StatusService
        end

        SigningService-->>Controller: كائن التوقيع (DocumentSignature)
        deactivate SigningService
        Controller-->>UI: تم التوقيع بنجاح وتحديث الحالة
        deactivate Controller
        UI-->>Signer: عرض نجاح التوقيع وتحديث الصفحة
        deactivate UI
    end
```

---

## 6. مخطط التتابع: إصدار PDF والتحقق عبر QR (Sequence Diagram 5)
### عنوان المخطط: مخطط التتابع لإصدار الوثيقة والتحقق عبر QR

```mermaid
sequenceDiagram
    autonumber
    actor Admin as مدير النظام / المسؤول (Admin)
    participant UI as لوحة التحكم (Admin UI)
    participant Controller as RequestController
    participant IssuanceService as DocumentIssuanceService
    participant DomPDF as DomPDF Engine
    participant QRGen as SimpleSoftwareIO QR
    participant Storage as التخزين (Private Disk)
    participant DocModel as IssuedDocument (Model)
    actor Graduate as الخريج (Graduate)
    actor Visitor as زائر التحقق (Verification Visitor)
    participant VerifyUI as صفحة التحقق العامة (Verification Page)
    participant VerifyController as VerificationController

    rect rgb(240, 240, 240)
        note right of Admin: توليد وإصدار الوثيقة (الـ PDF والـ QR)
        Admin->>UI: النقر على "إصدار الوثيقة / توليد الـ PDF"
        activate UI
        UI->>Controller: generatePdf(documentRequest)
        activate Controller
        Controller->>IssuanceService: issue(documentRequest, adminId)
        activate IssuanceService
        IssuanceService->>QRGen: generate(verifyUrl)
        activate QRGen
        QRGen-->>IssuanceService: صورة الـ QR بصيغة SVG
        deactivate QRGen
        IssuanceService->>DomPDF: loadView(template, data)
        activate DomPDF
        DomPDF-->>IssuanceService: ملف PDF كـ Binary
        deactivate DomPDF
        IssuanceService->>Storage: put("documents/serial.pdf", pdfContent)
        activate Storage
        Storage-->>IssuanceService: تم الحفظ بنجاح
        deactivate Storage
        IssuanceService->>DocModel: updateOrCreate(...)
        activate DocModel
        DocModel-->>IssuanceService: كائن الوثيقة الصادرة
        deactivate DocModel
        IssuanceService-->>Controller: الوثيقة الصادرة
        deactivate IssuanceService
        Controller-->>UI: تم توليد الـ PDF وحفظه بنجاح
        deactivate Controller
        UI-->>Admin: عرض نجاح العملية
        deactivate UI
    end

    rect rgb(230, 245, 230)
        note right of Graduate: تحميل الوثيقة من قبل الخريج
        Graduate->>UI: النقر على زر "تحميل الوثيقة الصادرة"
        activate UI
        UI->>Controller: download(documentRequest)
        activate Controller
        Controller->>Storage: get(pdf_path)
        activate Storage
        Storage-->>Controller: ملف الـ PDF
        deactivate Storage
        Controller-->>UI: تحميل الملف (attachment)
        UI-->>Graduate: حفظ ملف الـ PDF على الجهاز
        deactivate Controller
        deactivate UI
    end

    rect rgb(230, 230, 250)
        note right of Visitor: التحقق عبر رمز الـ QR
        Visitor->>VerifyUI: مسح رمز الـ QR بالهاتف (أو إدخال رمز التتبع)
        activate VerifyUI
        VerifyUI->>VerifyController: show(token) / verify(Request)
        activate VerifyController
        VerifyController->>DocModel: البحث باستخدام qr_token / serial_number / tracking_code
        activate DocModel
        DocModel-->>VerifyController: كائن الوثيقة الصادرة وتفاصيلها الأكاديمية
        deactivate DocModel
        VerifyController-->>VerifyUI: عرض تفاصيل الطالب وصحة الوثيقة والجامعة
        deactivate VerifyController
        VerifyUI-->>Visitor: استعراض وثيقة الخريج المعتمدة والتحقق منها
        deactivate VerifyUI
    end
```

---

## 7. مخطط التعاون (Collaboration Diagram)
### عنوان المخطط: مخطط التعاون بين مكونات النظام

```mermaid
flowchart TD
    %% Objects
    Graduate["1: الخريج (Graduate)"]
    UI["2: واجهة طلب الوثيقة (Request UI)"]
    Controller["3: DocumentRequestController"]
    RequestModel["4: DocumentRequest (Model)"]
    Finance["5: المسؤول المالي (Finance Admin)"]
    Academic["6: المسؤول الأكاديمي (Academic Admin)"]
    Issuance["7: DocumentIssuanceService"]
    IssuedDoc["8: IssuedDocument (Model)"]
    Signers["9: الموقّعون (Signers)"]
    Engines["10: PDF/QR Engine"]

    %% Communications
    Graduate -->|1: تقديم الطلب ورفع الدفع| UI
    UI -->|2: store()| Controller
    Controller -->|3: create(SUBMITTED)| RequestModel
    Controller -.->|4: إشعار بطلب دفع جديد| Finance
    Finance -->|5: approve(اعتماد الدفع وتحول الحالة لـ UNDER_REVIEW)| Controller
    Academic -->|6: updateStatus(APPROVED)| Controller
    Academic -->|7: sendForSignatures(إرسال للتواقيع)| Controller
    Controller -->|8: initiateDraft()| Issuance
    Issuance -->|9: create(مسودة الوثيقة)| IssuedDoc
    Issuance -.->|10: إشعار الموقّع الأول| Signers
    Signers -->|11: signDocument()| Controller
    Controller -->|12: finalizeIfComplete() [التوقيع الأخير]| Issuance
    Issuance -->|13: توليد الـ PDF والـ QR وحفظ الملف| Engines
    Issuance -->|14: updateStatus(ISSUED)| RequestModel
    Controller -.->|15: إشعار بجاهزية الوثيقة| Graduate
```

---

## 8. مخطط الحالة لطلب الوثيقة (State Chart Diagram)
### عنوان المخطط: مخطط حالات طلب الوثيقة

```mermaid
stateDiagram-v2
    [*] --> SUBMITTED : تقديم طلب جديد

    state "تم تقديم الطلب (SUBMITTED)" as SUBMITTED
    state "قيد المراجعة (UNDER_REVIEW)" as UNDER_REVIEW
    state "تمت الموافقة الأكاديمية (APPROVED)" as APPROVED
    state "بانتظار التوقيعات (PENDING_SIGNATURES)" as PENDING_SIGNATURES
    state "جاهزة للإصدار (READY)" as READY
    state "تم الإصدار (ISSUED)" as ISSUED
    state "مرفوض (REJECTED)" as REJECTED

    SUBMITTED --> UNDER_REVIEW : اعتماد الدفع من المالي
    SUBMITTED --> REJECTED : رفض إثبات الدفع

    UNDER_REVIEW --> APPROVED : اعتماد البيانات الأكاديمية
    UNDER_REVIEW --> REJECTED : رفض البيانات الأكاديمية

    APPROVED --> PENDING_SIGNATURES : إرسال الوثيقة للتواقيع (بدء مسار التوقيع)

    PENDING_SIGNATURES --> ISSUED : التوقيع من الموقّع الأخير (تلقائياً)
    PENDING_SIGNATURES --> READY : اعتماد يدوي من المسؤول
    PENDING_SIGNATURES --> REJECTED : إلغاء/رفض الطلب

    READY --> ISSUED : تسليم وتحميل الوثيقة
    READY --> PENDING_SIGNATURES : إعادة إصدار لإعادة التواقيع

    ISSUED --> PENDING_SIGNATURES : إعادة إصدار وتعديل بيانات الطلب

    REJECTED --> [*]
    ISSUED --> [*]
```

---

## 9. مخطط الأنشطة العام لطلب الوثيقة (Activity Diagram)
### عنوان المخطط: مخطط النشاط العام لطلب الوثيقة

```mermaid
flowchart TD
    Start([البداية]) --> Login[تسجيل دخول الخريج]
    Login --> Choice[اختيار نوع الوثيقة واللغة]
    Choice --> CheckData{هل السجل الأكاديمي مدخل؟}
    
    CheckData -- لا --> Error[عرض رسالة خطأ: مراجعة شؤون الطلاب] --> End([النهاية])
    CheckData -- نعم --> CreateReq[إنشاء طلب وثيقة جديد]
    
    CreateReq --> CheckPay{هل الدفع مطلوب للوثيقة؟}
    CheckPay -- نعم --> UploadProof[رفع صورة إثبات الدفع]
    UploadProof --> SaveSubmitted[حفظ الطلب كـ SUBMITTED والمالية كـ pending_review]
    SaveSubmitted --> ReviewPay[مراجعة إثبات الدفع من المسؤول المالي]
    ReviewPay --> IsPayValid{هل إثبات الدفع صحيح؟}
    IsPayValid -- لا --> RejectPay[تحديث الدفع لـ rejected وإشعار الخريج] --> UploadProof
    IsPayValid -- نعم --> ApprovePay[تحديث الدفع لـ approved والحالة لـ UNDER_REVIEW]
    
    CheckPay -- لا --> SaveUnderReview[حفظ الطلب كـ UNDER_REVIEW ودون دفع]
    
    ApprovePay --> AcademicReview[مراجعة البيانات الأكاديمية من المسؤول الأكاديمي]
    SaveUnderReview --> AcademicReview
    
    AcademicReview --> IsDataValid{هل البيانات صحيحة؟}
    IsDataValid -- لا --> RejectReq[تحديث حالة الطلب لـ REJECTED وإشعار الخريج] --> End
    IsDataValid -- نعم --> ApproveReq[تحديث حالة الطلب لـ APPROVED]
    
    ApproveReq --> SendSign[النقر على إرسال لسير التوقيعات]
    SendSign --> InitiateDraft[تحديث الطلب لـ PENDING_SIGNATURES وإنشاء مسودة]
    
    InitiateDraft --> LoopSign{هل يتبقى موقّعون في السلسلة؟}
    LoopSign -- نعم --> SignCurrent[تنبيه الموقّع الحالي ويقوم بالتوقيع إلكترونياً] --> LoopSign
    LoopSign -- لا --> FinalizePdf[توليد وحفظ ملف PDF النهائي مع التواقيع ورمز QR]
    
    FinalizePdf --> AutoIssued[تحديث حالة الطلب تلقائياً إلى ISSUED]
    AutoIssued --> NotifyGrad[إرسال إشعار للخريج وتنزيل الوثيقة PDF]
    NotifyGrad --> QRScan[التحقق اللاحق من الوثيقة عبر مسح رمز الـ QR]
    QRScan --> End
```

---

## 10. مخطط الأنشطة بالمسارات (Swimlanes Activity Diagram)
### عنوان المخطط: مخطط النشاط بالمسارات لنظام بوابة خدمات الخريجين

```mermaid
flowchart TD
    %% Swimlanes as subgraphs
    subgraph Graduate [الخريج]
        G_start([البداية]) --> G_login[تسجيل الدخول وطلب وثيقة]
        G_login --> G_pay_check{تتطلب دفعاً؟}
        G_pay_check -- نعم --> G_upload[رفع إثبات الدفع]
        G_download[تحميل الوثيقة الصادرة PDF]
        G_use[تقديم الوثيقة للجهات الخارجية]
    end

    subgraph System [النظام]
        S_save_sub[حفظ كـ SUBMITTED وإشعار المالية]
        S_save_rev[حفظ كـ UNDER_REVIEW وإشعار الطالب]
        S_save_pend[حفظ كـ PENDING_SIGNATURES وإشعار الموقّع الأول]
        S_register_sig[تسجيل التوقيع والتحقق من الترتيب التسلسلي]
        S_next_notif[إشعار الموقّع التالي]
        S_finalize[توليد الـ PDF النهائي وتحديث الحالة لـ ISSUED وإشعار الخريج]
        S_verify[التحقق من التوكن وصحة الوثيقة في قاعدة البيانات]
    end

    subgraph FinanceAdmin [المسؤول المالي]
        F_review[مراجعة إثبات الدفع والاعتماد]
    end

    subgraph AcademicAdmin [المسؤول الأكاديمي]
        A_review[تدقيق السجل الأكاديمي والاعتماد APPROVED]
        A_send[النقر على إرسال لسير التوقيعات]
    end

    subgraph Signers [الموقّعون]
        Sig_action[مراجعة تفاصيل المستند والتوقيع إلكترونياً]
    end

    subgraph Verifier [جهة التحقق]
        V_scan[مسح رمز الـ QR المطبوع على الوثيقة]
        V_result[استعراض تفاصيل الوثيقة الرسمية المؤكدة]
        V_end([النهاية])
    end

    %% Flow across swimlanes
    G_pay_check -- لا --> S_save_rev
    G_upload --> S_save_sub
    S_save_sub --> F_review
    F_review --> S_save_rev
    S_save_rev --> A_review
    A_review --> A_send
    A_send --> S_save_pend
    S_save_pend --> Sig_action
    Sig_action --> S_register_sig
    S_register_sig --> Sig_loop{هل توجد تواقيع متبقية؟}
    Sig_loop -- نعم --> S_next_notif --> Sig_action
    Sig_loop -- لا --> S_finalize
    S_finalize --> G_download
    G_download --> G_use
    G_use --> V_scan
    V_scan --> S_verify
    S_verify --> V_result
    V_result --> V_end
```

---

## 11. مخطط الطبقات (Layer Diagram)
### عنوان المخطط: مخطط طبقات نظام بوابة خدمات الخريجين

```mermaid
flowchart TD
    subgraph Presentation [طبقة العرض - Presentation Layer]
        views["Blade Views (Arabic/RTL)"]
        css["Bootstrap 5 / CSS"]
        js["Alpine.js / JavaScript"]
        flatpickr["Flatpickr (Gregorian Picker)"]
        icons["FontAwesome Icons"]
    end

    subgraph Control [طبقة التحكم والتوجيه - Control Layer]
        routes["Web Routes (web.php)"]
        middleware["Middlewares (role, permission, throttle)"]
        auth_ctrl["AuthController"]
        req_ctrl["DocumentRequestController"]
        verify_ctrl["VerificationController"]
        admin_ctrl["Admin Controllers"]
        employer_ctrl["Employer Controllers"]
    end

    subgraph Business [طبقة منطق الأعمال - Business Logic Layer]
        sign_srv["DocumentSigningService"]
        issue_srv["DocumentIssuanceService"]
        status_srv["RequestStatusService"]
        student_srv["StudentInformationService"]
        drivers["StudentInfo Drivers (Api, Database, Excel)"]
    end

    subgraph Model [طبقة النماذج - Model/Eloquent Layer]
        user_m["User Model"]
        grad_m["Graduate Model"]
        req_m["DocumentRequest Model"]
        doc_m["IssuedDocument Model"]
        sig_m["DocumentSignature Model"]
        academic_m["AcademicRecord Models (Levels, Semesters, Subjects)"]
        grades_m["GradesCertificate Models"]
        job_m["Job / Application Models"]
    end

    subgraph StorageLayer [طبقة قاعدة البيانات والتخزين - DB & Storage Layer]
        sqlite[(SQLite Database)]
        private_store["Private Storage (local disk)"]
        public_store["Public Storage (public disk)"]
    end

    subgraph External [الخدمات المساندة والمكتبات - Supporting Services]
        dompdf["DomPDF Engine"]
        qr_gen["SimpleSoftwareIO QR Generator"]
        notif_sys["Laravel Notifications System"]
        excel_sys["Maatwebsite Excel Import/Export"]
    end

    %% Relations
    Presentation --> Control
    Control --> Business
    Business --> Model
    Model --> StorageLayer
    Business -.-> External
```

---

## 12. مخطط العلاقات والكيانات (ERD Diagram)
### عنوان المخطط: مخطط الكيانات والعلاقات لقاعدة البيانات

```mermaid
erDiagram
    users {
        int id PK
        string name
        string email
        string password
        string role
        boolean is_active
        string signature_image
        string signer_role
        timestamp created_at
        timestamp updated_at
    }

    graduates {
        int user_id PK, FK
        string university_id
        string phone
        int major_id FK
        year graduation_year
        string photo
        string cv_path
        timestamp created_at
        timestamp updated_at
    }

    approved_graduates {
        int id PK
        string university_id
        string name
        string email
        string major
        string college
        integer graduation_year
        boolean is_frozen
        timestamp created_at
        timestamp updated_at
    }

    faculties {
        int id PK
        string name_ar
        string name_en
        text description
        string status
        timestamp created_at
        timestamp updated_at
    }

    majors {
        int id PK
        int faculty_id FK
        string name_ar
        string name_en
        string degree_ar
        string degree_en
        timestamp created_at
        timestamp updated_at
    }

    document_types {
        int id PK
        string name_ar
        string name_en
        string code
        decimal fee_amount
        string currency
        boolean payment_required
        timestamp created_at
        timestamp updated_at
    }

    document_requests {
        int id PK
        int user_id FK
        int document_type_id FK
        string tracking_code
        string language
        string purpose
        string delivery_type
        string status
        text admin_note
        string payment_status
        string payment_proof_path
        int payment_reviewed_by FK
        timestamp payment_reviewed_at
        text payment_rejection_reason
        decimal fee_amount
        string currency
        timestamp created_at
        timestamp updated_at
    }

    issued_documents {
        int id PK
        int document_request_id FK
        string serial_number
        string qr_token
        string pdf_path
        timestamp issued_at
        boolean is_valid
        timestamp revoked_at
        timestamp all_signed_at
        timestamp created_at
        timestamp updated_at
    }

    document_signatures {
        int id PK
        int issued_document_id FK
        int user_id FK
        string role_title
        timestamp signed_at
        string ip_address
        timestamp created_at
        timestamp updated_at
    }

    request_status_logs {
        int id PK
        int document_request_id FK
        int admin_id FK
        string from_status
        string to_status
        text note
        timestamp created_at
    }

    graduate_academic_records {
        int id PK
        int user_id FK
        string student_name_ar
        string student_name_en
        string university_number
        string degree_ar
        string degree_en
        string total_marks
        string gpa
        string overall_rating
        string honors_rank
        string graduation_year_label
        string enrollment_year_label
        string exam_session
        timestamp created_at
        timestamp updated_at
    }

    graduate_academic_levels {
        int id PK
        int graduate_academic_record_id FK
        int sort_order
        string name
        string academic_year
        string level_avg
        string final_result
        timestamp created_at
        timestamp updated_at
    }

    graduate_academic_semesters {
        int id PK
        int graduate_academic_level_id FK
        int sort_order
        timestamp created_at
        timestamp updated_at
    }

    graduate_academic_subjects {
        int id PK
        int graduate_academic_semester_id FK
        int sort_order
        string catalog_key
        string name
        string credit_hours
        string score
        string rating
        timestamp created_at
        timestamp updated_at
    }

    grades_certificates {
        int id PK
        int user_id FK
        string student_name_ar
        string student_name_en
        string university_number
        string degree_ar
        string degree_en
        string total_marks
        string gpa
        string overall_rating
        string honors_rank
        string graduation_year_label
        string enrollment_year_label
        string exam_session
        timestamp created_at
        timestamp updated_at
    }

    grades_certificate_levels {
        int id PK
        int grades_certificate_id FK
        int sort_order
        string name
        string academic_year
        string level_avg
        string final_result
        timestamp created_at
        timestamp updated_at
    }

    grades_certificate_semesters {
        int id PK
        int grades_certificate_level_id FK
        int sort_order
        timestamp created_at
        timestamp updated_at
    }

    grades_certificate_subjects {
        int id PK
        int grades_certificate_semester_id FK
        int sort_order
        string catalog_key
        string name
        string credit_hours
        string score
        string rating
        timestamp created_at
        timestamp updated_at
    }

    portal_jobs {
        int id PK
        int employer_id FK
        string title
        text description
        text requirements
        date deadline
        string location
        string job_type
        string status
        text rejection_reason
        timestamp closed_at
        boolean is_filled
        timestamp filled_at
        timestamp created_at
        timestamp updated_at
    }

    job_applications {
        int id PK
        int job_id FK
        int graduate_id FK
        text cover_letter
        string cv_path
        string status
        text employer_notes
        timestamp interview_date
        text interview_notes
        timestamp created_at
        timestamp updated_at
    }

    employers {
        int user_id PK, FK
        string company_name
        string phone
        string status
        string industry
        text description
        timestamp created_at
        timestamp updated_at
    }

    %% Relationships
    users ||--o| graduates : "has"
    users ||--o| employers : "has"
    faculties ||--o{ majors : "has"
    majors ||--o{ graduates : "has"
    users ||--o{ document_requests : "submits"
    document_types ||--o{ document_requests : "defines"
    users ||--o{ document_requests : "reviews payment"
    document_requests ||--o| issued_documents : "produces"
    issued_documents ||--o{ document_signatures : "requires"
    users ||--o{ document_signatures : "signs"
    document_requests ||--o{ request_status_logs : "has log"
    users ||--o{ request_status_logs : "actions"
    users ||--o| graduate_academic_records : "has"
    graduate_academic_records ||--o{ graduate_academic_levels : "has"
    graduate_academic_levels ||--o{ graduate_academic_semesters : "has"
    graduate_academic_semesters ||--o{ graduate_academic_subjects : "has"
    users ||--o| grades_certificates : "has"
    grades_certificates ||--o{ grades_certificate_levels : "has"
    grades_certificate_levels ||--o{ grades_certificate_semesters : "has"
    grades_certificate_semesters ||--o{ grades_certificate_subjects : "has"
    users ||--o{ portal_jobs : "posts"
    portal_jobs ||--o{ job_applications : "receives"
    users ||--o{ job_applications : "applies"
```

---

## 13. مخطط المكونات (Component Diagram)
### عنوان المخطط: مخطط المكونات لنظام بوابة خدمات الخريجين

```mermaid
flowchart TD
    subgraph Frontend [واجهة المستخدم - Frontend]
        views["Blade Views (واجهات الويب)"]
        styles["CSS & Alpine JS"]
    end

    subgraph Control [التحكم والأمن - Controller & Routing]
        req_ctrl["DocumentRequestController"]
        sign_ctrl["SignatureController"]
        pay_ctrl["PaymentReviewController"]
        verify_ctrl["VerificationController"]
    end

    subgraph Business [منطق الأعمال والخدمات - Business Logic]
        issue_srv["DocumentIssuanceService"]
        sign_srv["DocumentSigningService"]
        status_srv["RequestStatusService"]
        student_srv["StudentInformationService"]
    end

    subgraph Data [النماذج والبيانات - Data Subsystem]
        models["Eloquent Models (النماذج)"]
        db[("SQLite Database")]
        storage["Storage Engine (التخزين)"]
    end

    subgraph ThirdParty [المكونات الخارجية - Third-party Engines]
        dompdf["DomPDF Engine (توليد PDF)"]
        qr_gen["SimpleSoftwareIO QR (توليد QR)"]
        notif["Laravel Notification System (الإشعارات)"]
    end

    %% Links
    views --> req_ctrl
    views --> sign_ctrl
    views --> pay_ctrl
    views --> verify_ctrl

    req_ctrl -.-> styles
    req_ctrl --> issue_srv
    req_ctrl --> notif
    sign_ctrl --> sign_srv
    pay_ctrl --> status_srv
    pay_ctrl --> notif
    verify_ctrl --> models

    issue_srv --> status_srv
    issue_srv --> student_srv
    issue_srv --> dompdf
    issue_srv --> qr_gen
    issue_srv --> storage

    sign_srv --> status_srv
    sign_srv --> notif
    status_srv --> notif
    status_srv --> models

    models --> db
    storage --> db
```

---

## 14. مخطط النشر (Deployment Diagram)
### عنوان المخطط: مخطط النشر لنظام بوابة خدمات الخريجين

```mermaid
flowchart TD
    subgraph ClientDevice [جهاز المستخدم / العميل]
        browser["متصفح الويب (HTML5, CSS, Alpine.js)"]
    end

    subgraph ServerNode [خادم تطبيق Laravel]
        subgraph WebServer [خادم الويب Apache/Nginx]
            laravel["Laravel 11 & PHP 8.2 Backend"]
            pdf_engine["DomPDF Engine"]
            qr_engine["SimpleSoftwareIO QR Library"]
        end

        subgraph LocalDisk [نظام الملفات - Storage Disk]
            proofs["payment-proofs/ (إثباتات الدفع)"]
            pdf_dir["documents/ (ملفات الوثائق الصادرة)"]
            sig_dir["signatures/ (صور التواقيع)"]
        end
    end

    subgraph DbNode [خادم قاعدة البيانات]
        sqlite[("SQLite Database File (database.sqlite)")]
    end

    %% Network Connections
    browser <==>|HTTPS / Port 443| laravel
    laravel --> pdf_engine
    laravel --> qr_engine
    laravel --> LocalDisk
    pdf_engine --> pdf_dir
    laravel --> proofs
    laravel --> sig_dir
    laravel <==>|Eloquent ORM| sqlite
```
