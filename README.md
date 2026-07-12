# مخططات UML لبوابة خدمات الخريجين باستخدام PlantText (مستوى متوسط)

يحتوي هذا الملف على مخططات UML الأساسية لنظام **بوابة خدمات الخريجين - جامعة إقليم سبأ** بمستوى تفصيل متوسط (لا طويل ومعقد، ولا بسيط ومختصر)، مكتوبة بلغة **PlantUML** وجاهزة تماماً للاستخدام على موقع **PlantText** للتوثيق ومناقشة مشروع التخرج.

---

## 1. مخطط حالات الاستخدام (Use Case Diagram)

يوضح هذا المخطط الفاعلين الرئيسيين في النظام، حالات الاستخدام، وعلاقات الاشتمال والامتداد دون تعقيد زائد.

```plantuml
@startuml
title مخطط حالات الاستخدام (متوسط التفصيل)
left to right direction
skinparam actorStyle awesome

actor "الخريج (Graduate)" as graduate
actor "المسؤول المالي (Finance Admin)" as finance
actor "المسؤول الأكاديمي (Academic Admin)" as academic
actor "الموقّع الإلكتروني (Signer)" as signer
actor "مدير النظام (Super Admin)" as super_admin
actor "جهة التوظيف (Employer)" as employer

rectangle "بوابة خدمات الخريجين" {
    usecase "تقديم طلب وثيقة" as UC_request_doc
    usecase "رفع إثبات الدفع" as UC_upload_payment
    usecase "متابعة حالة الطلب" as UC_track_request
    usecase "مراجعة إثبات الدفع" as UC_review_payment
    usecase "المراجعة الأكاديمية" as UC_academic_review
    usecase "إدخل/تعديل السجل الأكاديمي" as UC_manage_record
    usecase "التوقيع الإلكتروني التسلسلي" as UC_sign_sequentially
    usecase "إصدار الوثيقة والـ PDF" as UC_generate_pdf
    usecase "تحميل الوثيقة الموقعة" as UC_download_pdf
    usecase "التحقق عبر رمز QR" as UC_verify_qr
    usecase "إدارة فرص العمل" as UC_manage_jobs
    usecase "إدارة الحسابات والصلاحيات" as UC_manage_users
}

graduate --> UC_request_doc
graduate --> UC_track_request
graduate --> UC_download_pdf
graduate --> UC_verify_qr

finance --> UC_review_payment

academic --> UC_academic_review
academic --> UC_manage_record

signer --> UC_sign_sequentially

super_admin --> UC_generate_pdf
super_admin --> UC_manage_users

employer --> UC_manage_jobs

UC_request_doc <.. UC_upload_payment : <<extend>> (إذا كانت مدفوعة)
UC_academic_review ..> UC_manage_record : <<include>>
UC_sign_sequentially ..> UC_generate_pdf : <<include>>
@enduml
```

---

## 2. مخطط التتابع (Sequence Diagram)

يوضح التفاعلات وتدفق البيانات الزمني بين الأطراف الفاعلة والنظام لإتمام مراحل طلب الوثيقة حتى توليد ملف الـ PDF.

```plantuml
@startuml
title مخطط التتابع لدورة حياة طلب الوثيقة

actor "الخريج (Graduate)" as Graduate
boundary "واجهة النظام (UI)" as UI
control "متحكم الطلبات (Controller)" as Ctrl
database "قاعدة البيانات (DB)" as DB
actor "المسؤول المالي (Finance)" as Finance
actor "المسؤول الأكاديمي (Academic)" as Academic
actor "الموقّع (Signer)" as Signer

Graduate -> UI: تقديم طلب وثيقة وإرفاق إثبات الدفع
activate UI
UI -> Ctrl: store(RequestData)
activate Ctrl
Ctrl -> DB: حفظ الطلب (حالة: SUBMITTED)
DB --> Ctrl: تأكيد الحفظ
Ctrl -> UI: عرض نجاح التقديم وإرسال إشعار للمالية
deactivate Ctrl
deactivate UI

Finance -> UI: مراجعة إثبات الدفع
activate UI
UI -> Ctrl: approvePayment()
activate Ctrl
Ctrl -> DB: تحديث الحالة (payment_status: approved)
DB --> Ctrl: تأكيد التحديث
Ctrl -> UI: تحديث الحالة إلى (UNDER_REVIEW)
deactivate Ctrl
deactivate UI

Academic -> UI: المراجعة الأكاديمية وإدخال البيانات
activate UI
UI -> Ctrl: approveAcademic()
activate Ctrl
Ctrl -> DB: تحديث الحالة إلى (APPROVED) وبدء مسار التوقيع
DB --> Ctrl: تأكيد التحديث
Ctrl -> UI: إرسال إشعار للموقّع الأول (PENDING_SIGNATURES)
deactivate Ctrl
deactivate UI

loop لكل موقّع في السلسلة
    Signer -> UI: تسجيل الدخول وتوقيع المستند
    activate UI
    UI -> Ctrl: signDocument()
    activate Ctrl
    Ctrl -> DB: تسجيل التوقيع وتحديث الموقّع التالي
    DB --> Ctrl: تأكيد التوقيع
    Ctrl -> UI: تحديث حالة التوقيعات
    deactivate Ctrl
    deactivate UI
end

Ctrl -> DB: توليد الرقم التسلسلي ورمز QR وحفظ مسار الـ PDF (الحالة: ISSUED)
Graduate -> UI: تحميل الوثيقة الموقعة والتحقق عبر QR
@enduml
```

---

## 3. مخطط حالة الكينونة (State Chart Diagram)

يوضح الحالات المختلفة التي يمر بها سجل الطلب داخل قاعدة البيانات والانتقالات المحكومة بالعمليات المالية والأكاديمية والتواقيع.

```plantuml
@startuml
title مخطط حالة الكينونة لطلب الوثيقة (State Chart Diagram)

[*] --> SUBMITTED : تقديم طلب جديد
SUBMITTED --> UNDER_REVIEW : رفع إثبات الدفع بنجاح
SUBMITTED --> REJECTED : إثبات الدفع مفقود أو غير مقروء

state UNDER_REVIEW {
    [*] --> PaymentPending
    PaymentPending --> PaymentApproved : اعتماد المسؤول المالي
    PaymentPending --> PaymentRejected : رفض الدفع
}

PaymentRejected --> REJECTED
PaymentApproved --> APPROVED : المراجعة الأكاديمية بنجاح
UNDER_REVIEW --> REJECTED : رفض البيانات الأكاديمية

APPROVED --> PENDING_SIGNATURES : بدء مسار التوقيع الإلكتروني
PENDING_SIGNATURES --> PENDING_SIGNATURES : توقيع الموقّع الحالي (تسلسلي)
PENDING_SIGNATURES --> READY : اكتمال كافة تواقيع السلسلة
READY --> ISSUED : اعتماد مدير النظام وتوليد PDF النهائي
ISSUED --> [*] : تحميل الوثيقة والتحقق عبر QR
REJECTED --> [*] : إغلاق الطلب مع ذكر السبب
@enduml
```

---

## 4. مخطط التعاون والتواصل (Collaboration Diagram)

يوضح العلاقات والرسائل المتبادلة بين كائنات النظام الأساسية مع ترقيمها لتوضيح تسلسل التفاعل.

```plantuml
@startuml
title مخطط التعاون والتواصل (Collaboration Diagram)

rectangle "Graduate: الخريج" as G
rectangle "UI: واجهة النظام" as UI
rectangle "Controller: متحكم العمليات" as Ctrl
rectangle "DB: قاعدة البيانات" as DB
rectangle "FinanceAdmin: المسؤول المالي" as F
rectangle "AcademicAdmin: المسؤول الأكاديمي" as A
rectangle "Signer: الموقّع" as S
rectangle "PDFService: خدمة المستندات" as PDF

G --> UI : 1. تقديم الطلب وإثبات الدفع\n11. تحميل الوثيقة والتحقق
UI --> Ctrl : 2. تمرير البيانات\n10. طلب ملف الـ PDF
Ctrl --> DB : 3. حفظ سجل الطلب\n6. تحديث حالة التدقيق\n8. حفظ التواقيع
F --> UI : 4. مراجعة واعتماد الدفع
Ctrl --> F : 5. إشعار الدفع الجديد
A --> UI : 6. مراجعة واعتماد البيانات الأكاديمية
S --> UI : 7. إجراء التوقيع الإلكتروني
Ctrl --> S : 7.1 إشعار بانتظار التوقيع
Ctrl --> PDF : 9. توليد المستند النهائي ورمز QR
@enduml
```

---

## 5. مخطط النشاط مع مسارات المسؤولية (Activity Diagram with Swimlanes)

يقسم المهام والأنشطة والمسؤوليات بناءً على دور كل مستخدم ونظام البوابة بشكل متسلسل وعامودي.

```plantuml
@startuml
title مخطط النشاط مع مسارات المسؤولية (Swimlanes)

|الخريج (Graduate)|
start
:تقديم طلب الوثيقة;
:إرفاق إثبات دفع الرسوم;

|نظام البوابة (System)|
:تسجيل الطلب وحفظ المرفقات;
:إرسال إشعار للمسؤول المالي;

|المسؤول المالي (Finance)|
:مراجعة إثبات الدفع المرفق;
if (هل الدفع صحيح وصالح؟) then (لا)
    :رفض الدفع وتسجيل السبب;
    |نظام البوابة (System)|
    :تحديث حالة الطلب إلى مرفوض وإرسال إشعار;
    |الخريج (Graduate)|
    :تلقي إشعار الرفض وتعديل الطلب;
    stop
else (نعم)
    :اعتماد الدفع وتحويل الطلب للمراجعة الأكاديمية;
endif

|المسؤول الأكاديمي (Academic)|
:تدقيق ومطابقة البيانات الأكاديمية للخرج;
if (هل البيانات مطابقة؟) then (لا)
    :رفض الطلب أكاديمياً;
    |نظام البوابة (System)|
    :تحديث الحالة إلى مرفوض وإعلام الخريج;
    stop
else (نعم)
    :اعتماد البيانات وبدء سلسلة التواقيع;
endif

|الموقّعون (Signers)|
repeat
    :الموقّع الحالي: مراجعة الوثيقة وتوقيعها إلكترونياً;
    |نظام البوابة (System)|
    :حفظ التوقيع وتحديث الموقّع التالي;
    |الموقّعون (Signers)|
backward:تنبيه الموقّع التالي;
repeat while (هل توجد تواقيع متبقية في السلسلة؟) is (نعم)
->لا;

|نظام البوابة (System)|
:توليد ملف PDF النهائي;
:توليد رمز التحقق QR والرقم التسلسلي;
:إرسال إشعار للخريج بجاهزية الوثيقة;

|الخريج (Graduate)|
:تحميل وثيقة التخرج الرسمية;
:التحقق من صحة الوثيقة عبر مسح QR;
stop
@enduml
```

---

## 6. مخطط العلاقات لقاعدة البيانات (ERD)

يوضح تصميم الجداول الرئيسية مع تحديد المفاتيح الأساسية (PK) والمفاتيح الأجنبية (FK) ونوع العلاقة بين الجداول.

```plantuml
@startuml
title مخطط العلاقات لقاعدة البيانات (ERD)
skinparam linetype ortho

entity "users (المستخدمين)" as users {
    * id : int [PK]
    --
    name : string
    email : string
    role : string
    signer_role : string
    signature_image : string
}

entity "graduates (الخريجين)" as graduates {
    * id : int [PK]
    --
    user_id : int [FK]
    university_number : string
    major_id : int [FK]
    gpa : decimal
}

entity "document_types (أنواع الوثائق)" as document_types {
    * id : int [PK]
    --
    name_ar : string
    name_en : string
    fee_amount : decimal
    payment_required : boolean
}

entity "document_requests (طلبات الوثائق)" as document_requests {
    * id : int [PK]
    --
    user_id : int [FK]
    document_type_id : int [FK]
    tracking_code : string
    status : string
    payment_status : string
    payment_proof_path : string
}

entity "issued_documents (الوثائق الصادرة)" as issued_documents {
    * id : int [PK]
    --
    document_request_id : int [FK]
    serial_number : string
    qr_token : string
    pdf_path : string
    all_signed_at : timestamp
}

entity "document_signatures (التواقيع)" as document_signatures {
    * id : int [PK]
    --
    issued_document_id : int [FK]
    user_id : int [FK]
    role_title : string
    signed_at : timestamp
}

entity "academic_records (السجلات الأكاديمية)" as academic_records {
    * id : int [PK]
    --
    user_id : int [FK]
    student_name_ar : string
    student_name_en : string
    gpa : decimal
    graduation_year : string
}

users ||--o| graduates : "يمتلك"
users ||--o{ document_requests : "يقدم"
document_types ||--o{ document_requests : "يحدد"
document_requests ||--o| issued_documents : "ينتج عنها"
issued_documents ||--o{ document_signatures : "تتطلب"
users ||--o{ document_signatures : "يوقّع"
users ||--o| academic_records : "له سجل"
@enduml
```

---

## 7. مخطط الفئات (Class Diagram)

يوضح الفئات والنماذج البرمجية، بالإضافة للخدمات (Services) الرئيسية مع أهم الخصائص والدوال المساعدة والعلاقات بينها.

```plantuml
@startuml
title مخطط الفئات (Class Diagram)

class User {
    +int id
    +string name
    +string email
    +string role
    +string signer_role
    +hasRole(role) : boolean
}

class Graduate {
    +int id
    +string university_number
    +decimal gpa
    +major() : Relation
}

class DocumentRequest {
    +int id
    +string tracking_code
    +string status
    +string payment_status
    +paymentProof() : string
    +approvePayment() : void
    +rejectPayment(reason) : void
}

class DocumentType {
    +int id
    +string name_ar
    +decimal fee_amount
    +isPaymentRequired() : boolean
}

class IssuedDocument {
    +int id
    +string serial_number
    +string qr_token
    +string pdf_path
    +getCurrentSigner() : string
    +isAllSigned() : boolean
}

class DocumentSignature {
    +int id
    +string role_title
    +timestamp signed_at
    +sign(userId) : void
}

class DocumentSigningService {
    +sign(issuedDocument, userId) : boolean
    +getRequiredSigners(documentType) : array
    +notifyCurrentSigner(issuedDocument) : void
}

class DocumentIssuanceService {
    +initiateDraft(documentRequest) : IssuedDocument
    +generateFinalPDF(issuedDocument) : string
    +createQRVerifyToken(issuedDocument) : string
}

User "1" -- "0..1" Graduate : owns
User "1" -- "*" DocumentRequest : submits
DocumentType "1" -- "*" DocumentRequest : defines
DocumentRequest "1" -- "0..1" IssuedDocument : triggers
IssuedDocument "1" -- "*" DocumentSignature : contains
User "1" -- "*" DocumentSignature : performs
DocumentSigningService ..> DocumentSignature : manages
DocumentIssuanceService ..> IssuedDocument : generates
@enduml
```

---

## 8. مواصفات حالات الاستخدام (Use Case Specifications)

توضح الجداول التالية التفاصيل التحليلية لحالات الاستخدام الأربعة الرئيسية في النظام.

### أ. تقديم طلب وثيقة (Submit Document Request)
| البند | التفاصيل |
| :--- | :--- |
| **الفاعل الرئيسي** | الخريج (Graduate) |
| **الوصف** | يقدم الخريج طلباً لوثيقة جديدة ويرفع صورة إثبات الرسوم. |
| **الشروط المسبقة** | وجود سجل أكاديمي مسبق للخريج في قاعدة البيانات. |
| **التدفق الرئيسي** | 1. يختار الخريج نوع الوثيقة واللغة وغرض التقديم.<br>2. يتحقق النظام من وجود بياناته الأكاديمية.<br>3. يرفع الخريج سند الدفع المالي ويؤكد الطلب.<br>4. يتم حفظ الطلب بحالة `SUBMITTED` وتنبيه المسؤول المالي. |
| **التدفق البديل** | إذا كانت الوثيقة مجانية، يتجاوز النظام خطوة رفع سند الدفع وتتحول الحالة إلى `APPROVED` لبدء المراجعة الأكاديمية مباشرة. |

### ب. مراجعة إثبات الدفع (Review Payment Proof)
| البند | التفاصيل |
| :--- | :--- |
| **الفاعل الرئيسي** | المسؤول المالي (Finance Admin) |
| **الوصف** | مراجعة واعتماد أو رفض سندات الدفع المرفقة بالطلبات. |
| **الشروط المسبقة** | أن يكون الطلب بحالة `SUBMITTED` وحالة الدفع `pending_review`. |
| **التدفق الرئيسي** | 1. يستعرض المسؤول المالي سند الدفع المرفق بالطلب.<br>2. يطابق السند مع حساب الجامعة البنكي.<br>3. يعتمد الدفع فتتغير حالة الدفع إلى `approved` وحالة الطلب إلى `UNDER_REVIEW`. |
| **التدفق البديل** | في حال عدم وضوح السند أو خطئه، يتم رفض الدفع مع إدخال السبب وإشعار الخريج لرفع سند جديد. |

### ج. المراجعة الأكاديمية (Academic Review)
| البند | التفاصيل |
| :--- | :--- |
| **الفاعل الرئيسي** | المسؤول الأكاديمي (Academic Admin) |
| **الوصف** | مطابقة بيانات ودرجات الخريج مع الكشوفات الورقية قبل التواقيع. |
| **الشروط المسبقة** | اعتماد الطلب مالياً وأن تكون حالته `UNDER_REVIEW`. |
| **التدفق الرئيسي** | 1. يراجع المدقق الأكاديمي درجات الخريج ومعدله التراكمي.<br>2. يعتمد البيانات أكاديمياً.<br>3. ينشئ النظام مسودة الوثيقة في جدول `issued_documents` وتتحول حالة الطلب لـ `APPROVED`. |
| **التدفق البديل** | إذا وجد المدقق خطأ في الدرجات، يتم رفض الطلب مع ذكر السبب وإعلام الخريج. |

### د. التوقيع الإلكتروني التسلسلي (Sequential Signing)
| البند | التفاصيل |
| :--- | :--- |
| **الفاعل الرئيسي** | الموقّعون (Signers - عميد، مسجل، إلخ حسب نوع الوثيقة) |
| **الوصف** | توقيع الوثيقة إلكترونياً بالتسلسل حسب الترتيب المحدد لكل نوع وثيقة. |
| **الشروط المسبقة** | أن تكون حالة الطلب `PENDING_SIGNATURES` ويكون الدور على الموقّع الحالي. |
| **التدفق الرئيسي** | 1. يراجع الموقّع الحالي بيانات الوثيقة الجاهزة.<br>2. يوقع إلكترونياً فيقوم النظام بحفظ التوقيع وتنبيه الموقّع التالي في الترتيب. |
| **التدفق البديل** | عند توقيع آخر مسؤول في السلسلة، يتم تحديث حقل `all_signed_at` وتتحول حالة الطلب تلقائياً إلى `READY` تمهيداً للإصدار النهائي. |
```
