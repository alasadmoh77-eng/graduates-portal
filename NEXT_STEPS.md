# NEXT_STEPS.md — Priority Tasks

## 1. Integration Safety Review (IMMEDIATE)

Before any new feature, verify:
- [ ] Notification dropdown opens without errors
- [ ] Notifications index page opens without errors
- [ ] No `Undefined array key` errors anywhere
- [ ] `signature_required` notifications display correctly
- [ ] Status update notifications display in Arabic
- [ ] Finance notifications display correctly
- [ ] Only current signer receives signature notification
- [ ] Finance users do NOT receive signature notifications
- [ ] Graduate does NOT receive `PENDING_SIGNATURES` notifications
- [ ] Pending signatures page shows `رقم الطلب` (tracking_code)
- [ ] PDF serial number unchanged
- [ ] QR verification unchanged
- [ ] Internal statuses remain English

## 2. Commit Stable State

After stability confirmed:
```bash
git status
git add .
git commit -m "Stabilize sequential signatures, notifications, and UI"
```

## 3. Excel Export for Requests

After stability, add Excel export for admin requests page:
- Export only visible requests based on role
- Respect active filters
- Arabic headers
- Use `maatwebsite/excel` (already installed)
- Do NOT change PDF/QR/lifecycle logic

## ⛔ Do NOT Start Until Integration Review Says Stable
