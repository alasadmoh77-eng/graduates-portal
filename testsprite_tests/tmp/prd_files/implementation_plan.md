# Phase D: Final Academic Polish

## Objective
Apply light, safe structural UI polish to existing views by adopting the unified component system (`x-page-header`, `x-section-card`, `x-empty-state`, etc.) established in Phase C.

## Scope & Target Files

| File | Proposed Change | Safety Rationale |
|---|---|---|
| `admin/dashboard.blade.php` | Replace static header with `<x-page-header>`. Wrap statistics section with `<x-section-card>`. | Modifies only layout wrappers and HTML structure. All dynamic data bindings (`Auth::user()`, total requests, etc.) remain intact. |
| `graduate/dashboard.blade.php` | Replace static banner with `<x-page-header>` and `<x-section-card>` containing the welcome text and primary actions. | Ensures consistent structural flow. No backend logic or route links are altered. |
| `employer/jobs/index.blade.php` | Add `<x-page-header>` for title/button. Apply `<x-empty-state>` for empty tables. Wrap the data table with `<x-section-card noPadding="true">`. | Pure UI improvement for empty data conditions and layout consistency. Keeps iteration logic unchanged. |
| `graduate/profile/show.blade.php` | Replace structural headers with `<x-page-header>`. Enclose Profile/Personal info chunks inside `<x-section-card>` elements. | Standardizes spacing and shadow rules without touching the underlying eloquent model calls (`$user->graduate`). |
| `jobs/index.blade.php` | Upgrade the `@empty` block's raw HTML to the unified `<x-empty-state>` component. Change manual alert block to `<x-alert-message>`. | Light drop-in replacement of existing visual blocks. Modals and loops are preserved entirely. |
| `events/index.blade.php` | Upgrade the `@empty` block's raw HTML to the unified `<x-empty-state>` component. Add `<x-page-header>`. | Preserves the visual design identity while centralizing empty-state generation. |

## Important Safety Rules Observed
- No backend logic or controllers will be altered.
- All existing loops (`@foreach`, `@forelse`) and conditionals will be strictly preserved.
- Existing translation keys (`__('app.key')`) will be retained and mapped into the new component slots/props.
- Modifications will be executed one file at a time.
- View cache will be cleared immediately after.

## User Review Required
Please review the targeted files and the proposed scope. Once approved, I will implement these light polish changes sequentially.
