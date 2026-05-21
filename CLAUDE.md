# CLAUDE.md — BIR Invoicing Backend API

This file gives Claude Code context about this project's conventions, structure, and development rules.

---

## Project Overview

Laravel 11 API backend for Philippine BIR-compliant invoicing. Multi-company, multi-user, with VAT calculation and BIR e-invoicing submission support.

See [docs/OVERVIEW.md](docs/OVERVIEW.md) for full architecture and domain documentation.

---

## Tech Stack

- **Framework**: Laravel 11, PHP 8.2+
- **Auth**: Laravel Sanctum (token-based API auth)
- **ORM**: Eloquent with soft deletes and encrypted fields
- **Database**: SQLite (default), supports MySQL/PostgreSQL
- **Testing**: PHPUnit 10 via `php artisan test`
- **Code style**: Laravel Pint (`vendor/bin/pint`)

---

## Commands

```bash
php artisan serve          # Start dev server
php artisan migrate        # Run migrations
php artisan test           # Run tests
vendor/bin/pint            # Format code
npm run dev                # Vite dev server
npm run build              # Build frontend assets
```

---

## Conventions

### PHP / Laravel

- Strict types (`declare(strict_types=1)`) on every PHP file
- Use typed enums in `app/Enums/` for all status and type fields — never raw strings
- Models live in `app/Models/`; keep business logic methods on models until a service layer exists
- Financial amounts are stored as `decimal(15, 2)` — never floats
- Sensitive fields (BIR API keys) use Laravel's `encrypted` cast
- Soft deletes on Company, Customer, Invoice — never hard-delete these
- Foreign keys use `constrained()->cascadeOnDelete()` or `nullOnDelete()` as appropriate

### Enums

All enums in `app/Enums/` are backed enums (`string` or `int`). They expose helper methods:
- `label()` — human-readable display name
- `color()` — UI color hint (where applicable)
- Permission checks like `canBeEdited()`, `canManageUsers()`

### Models

Each model documents its relationships via standard Eloquent (`hasMany`, `belongsTo`, `morphMany`). The `ActivityLog` model uses a polymorphic `loggable` relationship.

### Database

- All tables have `timestamps()`
- Add indexes for any column used in `WHERE`, `ORDER BY`, or foreign key lookups
- Run `php artisan migrate:fresh` only in local — never against shared/prod data

### Testing

- Feature tests go in `tests/Feature/`, unit tests in `tests/Unit/`
- Tests use SQLite in-memory database (configured in `phpunit.xml`)
- Do not mock Eloquent models — use factories and real database calls

---

## File Map (Key Files)

| Path | Purpose |
|------|---------|
| `app/Enums/` | 8 typed enums (InvoiceStatus, UserRole, etc.) |
| `app/Models/Company.php` | Multi-company root, invoice numbering logic |
| `app/Models/Invoice.php` | VAT calculation, BIR JSON generation |
| `app/Models/User.php` | Role-based access via UserRole enum |
| `app/Models/BirSetting.php` | Encrypted BIR API credentials per company |
| `app/Models/ActivityLog.php` | Polymorphic audit trail |
| `database/migrations/` | 12 ordered migrations |
| `routes/api.php` | API routes (Phase 4 — not yet implemented) |
| `routes/web.php` | Health check route (`/up`) |
| `config/sanctum.php` | Stateful domain config for Sanctum |

---

## What's Planned (Do Not Implement Speculatively)

- Phase 3: Repository interfaces + service layer + model observers
- Phase 4: RESTful controllers, form request validators, API resources
- Phase 5: Feature and unit test coverage

Only implement what is explicitly requested. Do not add routes, controllers, or services beyond the current phase.

---

## Security Notes

- BIR API keys are encrypted via Laravel's `encrypted` cast — never log or expose them
- Sanctum token scopes are not yet configured — add when implementing Phase 4
- Validate all user input at the form request layer, not in models or controllers directly
