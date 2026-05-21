# Architecture & Domain Overview

This document describes the domain model, data architecture, and design decisions for the BIR Invoicing Backend API.

---

## Domain Summary

The system manages invoicing for Philippine businesses that must comply with BIR (Bureau of Internal Revenue) e-invoicing requirements. It supports:

- Multiple companies, each with isolated data and their own BIR settings
- Customers (individual or business) linked to a company
- Invoices with line items, automatic VAT calculation, and BIR submission status
- Payment recording against invoices
- Role-based access control per company
- Full audit trail via polymorphic activity logging

---

## Data Model

### Entity Relationships

```
Company
 ├── has many Users (role-based)
 ├── has many Customers
 ├── has many Invoices
 │    ├── has many InvoiceItems
 │    ├── has many Payments
 │    └── belongs to Customer
 ├── has one BirSetting
 └── has many ActivityLogs (polymorphic)
```

### Models

#### Company
Central tenant entity. Stores BIR registration details, VAT rate, and controls invoice numbering via a locked transaction on `next_invoice_number`.

Key fields: `name`, `tin`, `vat_rate` (default 12%), `invoice_prefix`, `next_invoice_number`, `bir_registration`.

#### User
System user tied to a Company. Role is a `UserRole` enum with four levels:

| Role | Capabilities |
|------|-------------|
| `SUPER_ADMIN` | Full system access across companies |
| `COMPANY_ADMIN` | Full access within their company |
| `ACCOUNTANT` | Create/edit invoices, submit to BIR |
| `STAFF` | Create invoices, view reports |

#### Customer
Invoice recipient. Type is either `INDIVIDUAL` or `BUSINESS` (via `CustomerType` enum). Stores TIN, address, and contact info. Soft-deleted; never hard-removed.

#### Invoice
Core financial document. Tracks:
- `invoice_number` — auto-generated, unique per company
- `status` — `InvoiceStatus` enum: DRAFT → SENT → PAID / CANCELLED / OVERDUE
- `payment_status` — `PaymentStatus` enum: UNPAID, PARTIAL, PAID, OVERDUE
- `bir_status` — `BirStatus` enum: PENDING, SUBMITTED, ACCEPTED, REJECTED
- VAT amounts computed from line items
- `generateBirJson()` method produces BIR-compliant submission payload

#### InvoiceItem
Line item on an invoice. Computes:
- `line_total = quantity × unit_price − discount`
- `vat_amount` based on company VAT rate
- Supports `DiscountType`: NONE, PERCENTAGE, or FIXED

#### Payment
Records a payment event against an invoice. `PaymentMethod` enum: CASH, CHECK, TRANSFER, CREDIT_CARD, ONLINE. Updates invoice `payment_status` via model events (Phase 3).

#### BirSetting
Per-company BIR API configuration. API key and secret are stored using Laravel's `encrypted` cast. `BirMode` enum controls TEST vs PRODUCTION endpoint targeting.

#### ActivityLog
Polymorphic audit trail. Any model can log activity via `morphMany` to `ActivityLog`. Captures: `action`, `description`, `user_id`, `ip_address`, `user_agent`, and a JSON `changes` diff.

---

## Key Business Logic

### Invoice Numbering

Generating the next invoice number uses a database-level lock to prevent duplicates in concurrent requests:

```php
// Company::generateNextInvoiceNumber()
DB::transaction(function () {
    $company = Company::lockForUpdate()->find($this->id);
    $number = $company->invoice_prefix . str_pad($company->next_invoice_number, 8, '0', STR_PAD_LEFT);
    $company->increment('next_invoice_number');
    return $number;
});
```

### VAT Calculation

VAT is calculated at line-item level and summed at invoice level:

- `vatable_amount` = sum of taxable line totals
- `vat_amount` = `vatable_amount × (vat_rate / 100)`
- `total_amount` = `subtotal + vat_amount − discount`

The default VAT rate is 12% (Philippine standard), configurable per company.

### BIR JSON Generation

`Invoice::generateBirJson()` maps invoice data to the BIR e-invoicing schema, including company TIN, customer TIN, line items, VAT breakdown, and invoice metadata. Used by the BIR submission service (Phase 4).

---

## Enum Reference

| Enum | Values |
|------|--------|
| `InvoiceStatus` | DRAFT, SENT, PAID, CANCELLED, OVERDUE |
| `PaymentStatus` | UNPAID, PARTIAL, PAID, OVERDUE |
| `BirStatus` | PENDING, SUBMITTED, ACCEPTED, REJECTED |
| `CustomerType` | INDIVIDUAL, BUSINESS |
| `PaymentMethod` | CASH, CHECK, TRANSFER, CREDIT_CARD, ONLINE |
| `DiscountType` | NONE, PERCENTAGE, FIXED |
| `BirMode` | TEST, PRODUCTION |
| `UserRole` | SUPER_ADMIN, COMPANY_ADMIN, ACCOUNTANT, STAFF |

---

## Database Schema Notes

- All financial columns use `decimal(15, 2)` — never `float`
- Soft deletes (`deleted_at`) on Company, Customer, Invoice
- Cascading deletes: InvoiceItems and Payments cascade when their Invoice is deleted
- Foreign keys to Company use `nullOnDelete()` on Users (preserve user record if company removed)
- Indexes on: all foreign keys, `invoice_number`, `status`, `payment_status`, `bir_status`, `email`, `tin`

---

## Authentication

Laravel Sanctum issues API tokens. Each token is scoped to a User, which carries a `company_id` and `role`. Middleware will enforce:

1. Token validity (Sanctum)
2. Company membership
3. Role permissions (via `UserRole::permissions()`)

Token-based auth is configured; middleware and route guards are Phase 4 work.

---

## Implementation Phases

| Phase | Scope | Status |
|-------|-------|--------|
| 1 | Laravel setup, enums, migrations | Complete |
| 2 | Models, relationships, business logic methods | Complete |
| 3 | Repository pattern, service layer, model observers | In Progress |
| 4 | API routes, controllers, form requests, API resources | Planned |
| 5 | Feature tests, unit tests, CI pipeline | Planned |

---

## Directory Reference

```
app/Enums/                   # Typed enums
app/Http/Controllers/        # API controllers (Phase 4)
app/Models/                  # Eloquent models
database/migrations/         # Ordered schema migrations
routes/api.php               # API routes (Phase 4)
docs/OVERVIEW.md             # This file
```
