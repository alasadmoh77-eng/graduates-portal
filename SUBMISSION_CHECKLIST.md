# Submission Checklist

Use this checklist before uploading the project to GitHub or submitting it for review.

---

## Remove These Files / Directories

| Item | Reason |
|------|--------|
| `.env` | Contains database credentials, app key, and secrets |
| `.git/` | Local version history — start with a fresh clone |
| `vendor/` | Reinstall via `composer install` on the target machine |
| `node_modules/` | Reinstall via `npm install && npm run build` on the target machine |
| `storage/logs/` | Contains debug and error logs that may leak information |
| `storage/framework/cache/data/` | Cached data files — clear with `php artisan optimize:clear` |
| `storage/framework/views/*` | Compiled Blade templates — cleared by `php artisan view:clear` |
| `storage/debugbar/` | Debug bar data if present |
| `.phpunit.result.cache` | Local PHPUnit cache file |
| `database/database.sqlite` | SQLite database file (if using SQLite) — keep only the schema via migrations |

## Keep These Files

| File | Purpose |
|------|---------|
| `.env.example` | Template for environment configuration |
| `.env.docker` | Docker-specific environment config |
| `composer.json` | PHP dependency definitions |
| `composer.lock` | Locked PHP dependency versions |
| `package.json` | Node.js dependency definitions |
| `package-lock.json` | Locked Node.js dependency versions |
| `docker-compose.yml` | Docker orchestration configuration |
| `Dockerfile` | Docker image build instructions |
| `docker/` | Additional Docker configuration |
| `phpunit.xml` | PHPUnit test configuration |
| `vite.config.js` | Vite build configuration |
| `.gitattributes` | Git attribute rules |
| `.gitignore` | Git ignore rules |
| `README.md` | Project documentation |
| `walkthrough.md` | Application walkthrough guide |

## Commands to Run Before Submission

```bash
# Clear all cache
php artisan optimize:clear

# Run migrations and seeders (verify no errors)
php artisan migrate --seed

# Create storage link (if not already created)
php artisan storage:link

# Verify routes load correctly
php artisan route:list

# Run tests (if any)
php artisan test --quiet || echo "No tests configured"
```

## Verification Checklist

- [ ] `php artisan migrate --seed` runs without duplicate email errors
- [ ] `php artisan storage:link` creates the public/storage symlink
- [ ] All feature routes return 200 (dashboard, document requests, jobs, events, verification)
- [ ] PDF generation works for academic records and grades certificates
- [ ] QR code verification page displays correctly
- [ ] Admin dashboard shows all statistics without errors
- [ ] Contact and About alumni pages load correctly
- [ ] Arabic/English language switching works
- [ ] No hardcoded Computer Science text in any view or PDF template
