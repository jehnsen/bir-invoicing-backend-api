# BIR Invoicing Backend API

A Laravel 11 RESTful API for Philippine BIR (Bureau of Internal Revenue) compliant invoicing. Supports multi-company invoice management, VAT calculation, customer tracking, payment recording, and BIR e-invoicing submission.

---

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)
- [Project Structure](#project-structure)
- [Development Status](#development-status)
- [Testing](#testing)
- [License](#license)

---

## Requirements

- PHP 8.2+
- Composer 2.x
- Node.js 18+ and npm (for frontend assets)
- SQLite (default) or MySQL/PostgreSQL

---

## Installation

```bash
# Clone the repository
git clone <repository-url>
cd bir-invoicing-backend-api

# Install PHP dependencies
composer install

# Install frontend dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate
```

---

## Configuration

Edit `.env` for your environment. Key settings:

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_NAME` | Application name | `BIR Invoicing API` |
| `APP_ENV` | Environment (`local`, `production`) | `local` |
| `DB_CONNECTION` | Database driver | `sqlite` |
| `DB_DATABASE` | Database path/name | `database/database.sqlite` |
| `SANCTUM_STATEFUL_DOMAINS` | Allowed API client domains | `localhost` |

For BIR API integration, set per-company credentials via the `bir_settings` table (encrypted at rest).

---

## Running the Application

```bash
# Start the development server
php artisan serve

# Build frontend assets (if needed)
npm run build

# Or start Vite dev server
npm run dev
```

API base URL: `http://localhost:8000`

Health check: `GET /up`

---

## Project Structure

```
app/
├── Enums/                  # Typed enums for statuses and types
├── Http/Controllers/       # API controllers (Phase 4)
├── Models/                 # Eloquent models with relationships
└── Providers/              # Service providers
database/
├── migrations/             # 12 schema migrations
routes/
├── web.php                 # Web routes (health check)
└── api.php                 # API routes (Phase 4)
docs/
└── OVERVIEW.md             # Architecture and domain overview
```

---

## Development Status

| Phase | Description | Status |
|-------|-------------|--------|
| 1 | Foundation — Laravel setup, enums, migrations | Complete |
| 2 | Models & relationships, business logic | Complete |
| 3 | Repository pattern & service layer | In Progress |
| 4 | RESTful API controllers, routes, form requests | Planned |
| 5 | Feature & unit tests | Planned |

See [docs/OVERVIEW.md](docs/OVERVIEW.md) for full architecture documentation.

---

## Testing

```bash
# Run the full test suite
php artisan test

# Or via composer
composer test
```

---

## License

MIT
