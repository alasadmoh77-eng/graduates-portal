# CHANGELOG_NOTES.md — Recent Decisions

## Date: 2026-07-10

### Sequential Signature Workflow
- **Decision:** Signatures must be sequential, not parallel.
- **Implementation:** `IssuedDocument::getCurrentSigner()` returns first unsigned role in order.
- **Impact:** Only one signer can act at a time. Later signers see "ليس دورك الحالي".

### Signature Order
- **Academic Record:** المختص الأكاديمي → مدير شؤون الخريجين → مسجل الكلية → عميد الكلية
- **Grades Certificate:** مسجل الكلية → عميد الكلية → المسجل العام → نائب رئيس الجامعة لشؤون الطلاب

### Current Signer Notification
- **Decision:** Send `SignatureRequired` notification only to `getCurrentSigner()`.
- **Impact:** Prevents notification spam. Suppressed after final signer.

### Request Number Over Serial Number
- **Decision:** Pending signatures table shows `tracking_code` (رقم الطلب) not `serial_number`.
- **Impact:** Visual-only. PDF still uses official serial number.

### Arabic Status Labels — Display Only
- **Decision:** All internal status values remain English. Arabic labels in `lang/ar/app.php` only.
- **Impact:** Code comparisons (`=== 'APPROVED'`) unchanged. UI shows Arabic.

### Notification Safe Access
- **Decision:** All `$notification->data['key']` accesses guard with `?? null` or `!empty()`.
- **Impact:** No `Undefined array key` crashes regardless of notification type.

### Date Picker — Flatpickr over Native
- **Decision:** Do not use `<input type="date">` in Arabic RTL filter forms.
- **Solution:** `<input type="text" readonly>` + Flatpickr class `date-picker-input` with `disableMobile:true`.
- **Impact:** English Gregorian calendar always, no broken Arabic placeholders.

### Admin Role for Signatures
- **Decision:** 6 signer accounts created with `academic_admin` role and specific `signer_role`.
- **Accounts:** dean, registrar, graduates, specialist, general, vp — all @sru.edu.ye.

### Payment Notifications
- **Decision:** Finance notifications go ONLY to `finance_admin` role (not admin/super_admin).
- **Duplicate prevention:** Check for existing unread notification before sending.
