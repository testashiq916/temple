# Temple ERP API Reference

Base URL: `/api/v1`. All endpoints except `/auth/register` and `/auth/login`
require a Sanctum bearer token (`Authorization: Bearer <token>`) and are
scoped to the authenticated user's `company_id` (tenant).

## Auth

| Method | Path | Description |
| --- | --- | --- |
| POST | `/auth/register` | Create a new company (temple trust) + first admin user |
| POST | `/auth/login` | Returns `{ user, token }` |
| POST | `/auth/logout` | Revokes the current token |
| GET | `/auth/me` | Current user + company |

## Temple

- `GET/POST /temples`, `GET/PUT/DELETE /temples/{id}`
- `GET/POST /deities`, `GET/PUT/DELETE /deities/{id}`
- `GET/POST /festivals`, `GET/PUT/DELETE /festivals/{id}`

## Devotee

- `GET/POST /devotees`, `GET/PUT/DELETE /devotees/{id}` (supports `?search=`)
- `GET/POST /devotees/{devotee}/documents`
- `GET/POST /devotee-types`

## Seva

- `GET/POST /seva-categories`
- `GET/POST/PUT/DELETE /sevas`
- `GET/POST /sevas/{seva}/slots`
- `GET/POST /seva-bookings`, `GET /seva-bookings/{id}`
  - `POST /seva-bookings/{id}/confirm-payment` — posts the double-entry
    payment (dr Bank / cr Advance Seva Bookings)
  - `POST /seva-bookings/{id}/complete` — recognizes revenue (dr Advance /
    cr Seva Revenue)
  - `POST /seva-bookings/{id}/cancel` — releases the slot; if already paid,
    posts a credit-note refund entry
- `GET/POST/PUT /prasad-bookings`, `POST /prasad-bookings/{id}/confirm-payment`

## Donation

- `GET/POST /donation-categories`
- `GET/POST/PUT /donors`
- `GET/POST /donations`, `GET /donations/{id}`, `POST /donations/{id}/verify`
  — creation auto-posts a receipt + double-entry ledger voucher
- `GET/POST /e-hundi`
- `GET/POST /digital-gold`, `POST /digital-gold/{id}/redeem`
- `GET /receipts`, `GET /receipts/{id}`

## Financial / Accounting

- `GET /accounting/balance-sheet-heads`
- `GET /accounting/groups`
- `GET/POST /accounting/chart-of-accounts`
- `GET /accounting/ledger/{accode}` — per-account ledger + running balance
- `GET/POST /vouchers`, `GET /vouchers/{id}` — manual journal entries
  (legs must balance: total debits == total credits)
- `GET /reports/trial-balance`
- `GET /reports/income-expense`
- `GET /reports/dashboard`

## Crowd Management

- `GET/POST /queues`, `GET /queues/{id}`
- `POST /queues/{id}/entries`, `POST /queues/{id}/entries/{entry}/complete`, `POST /queues/{id}/close`
- `GET /crowd-analytics`, `GET /crowd-analytics/heatmap`
- `GET/POST /cameras`, `GET /cameras/{id}/alerts`, `POST /alerts/{id}/resolve`

## Property

- `GET/POST/PUT /properties`
- `GET/POST /properties/{property}/land-records`
- `GET/POST /properties/{property}/tenants`, `PATCH .../tenants/{tenant}`

## Inventory

- `GET/POST /inventory-categories`
- `GET/POST/PUT /inventory-items`
- `GET/POST /inventory-items/{item}/movements` — auto-adjusts item quantity

## Staff

- `GET/POST /staff-positions`
- `GET/POST/PUT /staff`
- `GET/POST/PUT /volunteers`

## Double-entry accounting

Every money-moving action posts matched debit/credit rows to `daybook`
through `App\Services\Accounting\LedgerService`, using the seeded chart of
accounts (see `AccountingSeeder`): `102` Bank, `107` Digital Gold Reserve,
`205` Advance Seva Bookings, `301` Donation Revenue, `302` Seva Revenue,
`303` Prasad Sales. `GET /reports/trial-balance` should always show
`total_debit == total_credit`.
