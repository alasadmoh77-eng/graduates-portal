# PROJECT_STATE.md — Current System State

## System Summary
The portal handles:
- Graduate document requests (Academic Record + Grades Certificate)
- Payment proof upload & finance review
- Academic data entry & review
- Document issuance with PDF generation & QR verification
- Signature workflow (sequential)
- Database notifications
- Jobs/employment module, employers, events, contact messages

## Internal Status Values (English — never change)

| Internal Value | Arabic Display |
|---------------|----------------|
| `SUBMITTED` | تم تقديم الطلب |
| `UNDER_REVIEW` | قيد المراجعة |
| `APPROVED` | تمت الموافقة |
| `PENDING_SIGNATURES` | بانتظار التوقيعات |
| `READY` | جاهزة للإصدار |
| `ISSUED` | تم الإصدار |
| `REJECTED` | مرفوض |

**All comparisons in code must use English values.** Arabic is display-only via `lang/ar/app.php`.

## Transition Flow
```
SUBMITTED → UNDER_REVIEW → APPROVED → PENDING_SIGNATURES → READY → ISSUED
                                   ↘ REJECTED (from any)    ↗ (admin approves)
```

## Completed Recent Changes

1. **Safe notification display** — all notification array keys use `?? null` fallback.
2. **Arabic status labels** — updated in `lang/ar/app.php` with display-only Arabic.
3. **Sequential signature workflow** — `IssuedDocument::getCurrentSigner()` returns first unsigned role.
4. **SignatureRequired notification** — sent only to current signer; suppressed after final signer.
5. **Pending signatures table** — shows request `tracking_code` instead of `serial_number`.
6. **Date filter** — uses Flatpickr with `date-picker-input` class for English Gregorian picker.
7. **Ready signatures** — shows completed documents with filters and Excel export.
8. **Request status checks** — READY blocked until all signatures complete.

## Key Files Recently Changed

| File | Purpose |
|------|---------|
| `app/Models/IssuedDocument.php` | `getCurrentSigner()`, `getRequiredSigners()` |
| `app/Services/DocumentSigningService.php` | Sequential signing, `notifyCurrentSigner()` |
| `app/Services/DocumentIssuanceService.php` | `initiateDraft()`, PDF finalization |
| `app/Services/RequestStatusService.php` | PENDING_SIGNATURES, READY check |
| `app/Notifications/SignatureRequired.php` | Signature notification class |
| `app/Http/Controllers/Admin/SignatureController.php` | Sign, pending, ready, approve-issue |
| `resources/views/admin/pending-signatures.blade.php` | Filters + tracking_code display |
| `resources/views/admin/ready-signatures.blade.php` | Filters + Excel export + tracking_code |
| `resources/views/layouts/app.blade.php` | Notification bell display |
| `resources/views/notifications/index.blade.php` | Notification page display |
| `lang/ar/app.php` | Arabic status translations |
| `components/status-badge.blade.php` | Status badges |
| `components/workflow-progress.blade.php` | Workflow progress bar |

## What Must Not Be Broken
- PDF output and templates
- QR verification
- Document serial number generation
- Request status lifecycle
- Signature sequence order
- Finance payment review
- Graduate document download
- Public homepage design
