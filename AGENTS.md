# AGENTS.md — OpenCode Instructions

## Project Identity
- **Name:** Graduation Services Portal — Saba Region University
- **Stack:** Laravel 11, PHP 8.2, Blade, SQLite, DomPDF, QR verification, database notifications
- **UI:** Arabic RTL with internal English status constants
- **Bundled assets:** Flatpickr (date picker), Bootstrap 5, FontAwesome, Alpine.js, Chart.js

## Critical Safety Rules
- ❌ Do NOT modify PDF templates unless explicitly requested.
- ❌ Do NOT modify PDF generation logic unless explicitly requested.
- ❌ Do NOT modify QR verification unless explicitly requested.
- ❌ Do NOT change database status values (they must remain English).
- ✅ Arabic labels are display-only via `lang/ar/app.php`.
- ❌ Do NOT redesign public UI unless explicitly requested.
- ❌ Do NOT change routes/controllers broadly without reporting first.
- ✅ Before editing any sensitive area, inspect and report files to be changed.

## Current Business Rules

### Signature Workflow (Sequential)
Academic Record order:
1. المختص الأكاديمي
2. مدير شؤون الخريجين
3. مسجل الكلية
4. عميد الكلية

Grades Certificate order:
1. مسجل الكلية
2. عميد الكلية
3. المسجل العام
4. نائب رئيس الجامعة لشؤون الطلاب

- Only the **current signer** can sign.
- After signing, the next signer becomes current.
- After the final signer → PDF generated → status stays PENDING_SIGNATURES until admin approves.

### Notification Rules
- **Finance:** payment proof review notifications only → `['finance_admin']` roles.
- **Current signer:** receives `signature_required` only when it is their turn.
- **Graduate:** receives only `APPROVED`, `REJECTED`, `READY`, `ISSUED` notifications.
- **Admin/academic:** receives relevant review/management notifications.
- All notification views must use safe access: `$data['key'] ?? null`.

### Date Filter Rule
- Do NOT use native `<input type="date">` for Arabic RTL filter fields.
- Use `<input type="text" readonly>` + Flatpickr class `date-picker-input` with `disableMobile:true`.

## Development Workflow
- Make one small change at a time.
- Report exact files before editing.
- After each change, run: `php artisan optimize:clear`
- Confirm PDF/QR/database were not touched after each change.
- Read `PROJECT_STATE.md` and `NEXT_STEPS.md` before starting new work.
