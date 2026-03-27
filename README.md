# World Cup Tickets API

A Laravel REST API for managing World Cup 2026 match tickets, with a focus on concurrency safety, atomic seat reservation, and async payment processing.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan queue:work
```

## Composer Scripts

| Command | What it does |
|---|---|
| `composer local-prepare` | Fresh install + `migrate:fresh --seed` |
| `composer testing-prepare` | Runs `migrate:fresh` against the testing environment (`.env.testing`) |
| `composer test` | Clears config cache then runs the full test suite |
| `composer dev` | Starts server, queue listener, log watcher, and Vite in parallel |
| `composer cs` | Runs PHP_CodeSniffer (check only) |
| `composer cbf` | Runs PHP Code Beautifier and Fixer (auto-fix) |

## Seed Credentials

All seeded users share the password: **`password`**

### Admin

| Name       | Email               | Role  |
|------------|---------------------|-------|
| Admin User | admin@worldcup.test | admin |

### Fans

| Name       | Email              | Role |
|------------|--------------------|------|
| Fan User 1 | fan1@worldcup.test | fan  |
| Fan User 2 | fan2@worldcup.test | fan  |
| Fan User 3 | fan3@worldcup.test | fan  |
| Fan User 4 | fan4@worldcup.test | fan  |
| Fan User 5 | fan5@worldcup.test | fan  |

## API Reference

### Authentication

```
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout       (auth required)
GET  /api/auth/me           (auth required)
```

Include the token in all authenticated requests:
```
Authorization: Bearer <token>
```

### Matches (public)

```
GET  /api/matches
GET  /api/matches/{id}
```

### Tickets (fan only)

```
GET  /api/tickets
GET  /api/tickets/{id}
```

### Reservations (fan only)

```
POST   /api/reservations                   reserve seats
POST   /api/reservations/{id}/pay          trigger payment
DELETE /api/reservations/{id}              cancel
```

### Admin

```
POST  /api/admin/matches
PATCH /api/admin/matches/{id}
GET   /api/admin/matches/{id}/report
```

---

## Design Decisions

### Repository Pattern

All database access goes through interfaces (`TicketCategoryRepositoryInterface`, `ReservationRepositoryInterface`, etc.) bound to concrete implementations in `RepositoryServiceProvider`. Services depend on interfaces, not Eloquent models directly. This keeps business logic testable and allows swapping implementations (e.g. switching from MySQL to another driver) without touching service code.

### Service Layer with Policy Authorization

Business logic and authorization live in service classes that extend `BaseService`, which uses Laravel's `AuthorizesRequests` trait. This means authorization (`$this->authorize('pay', $reservation)`) happens inside the service rather than the controller, so policy checks are enforced regardless of which controller — or future command/job — calls the service.

### Getter Methods Instead of Public Properties

Models expose data through explicit getter methods (`getId()`, `getEmail()`, `getRole()`) instead of relying on magic `__get`. This makes the model's public contract explicit and catches typos at compile-time rather than silently returning `null`.

### FormRequest Validation

Each route's input validation is isolated in its own `FormRequest` class (`RegisterRequest`, `LoginRequest`, `StoreReservationRequest`, etc.), keeping controllers thin and validation rules versioned alongside their routes.

---

## Concurrency Strategy

The core challenge: two fans simultaneously attempting to buy the last 2 tickets from a category with `available_count = 2`.

### Why Optimistic Locking Is Not Enough

An application-level check (`if ($category->available_count >= $quantity)`) creates a race condition — both requests can read `available_count = 2` before either has written, and both proceed to create reservations, overselling.

### Solution: Pessimistic Locking + Atomic Conditional UPDATE

Two-step approach inside a single `DB::transaction()`:

**Step 1 — Row lock (`SELECT ... FOR UPDATE`)**

```php
TicketCategory::lockForUpdate()->findOrFail($id);
```

This acquires an exclusive row lock on the ticket category. Concurrent requests block here until the lock is released.

**Step 2 — Atomic decrement with constraint**

```php
$affected = TicketCategory::where('id', $id)
    ->where('available_count', '>=', $amount)
    ->decrement('available_count', $amount);

if ($affected === 0) {
    throw new InsufficientSeatsException();
}
```

The `WHERE available_count >= $amount` condition is evaluated and the decrement applied atomically by the database. If the condition fails (seats were taken by a concurrent transaction that completed first), `$affected` is 0 and we throw immediately. No PHP-level re-check is needed — the database enforces the constraint.

**Why both steps?**

`lockForUpdate()` prevents two transactions from decrementing at the same time. The conditional `UPDATE` is the safety net: even if the lock were somehow bypassed, `available_count` can never go negative because the constraint is in the `WHERE` clause of the `UPDATE`, not in PHP.

### Status Transition Safety

`confirm()`, `cancel()`, and `expire()` in `ReservationService` all follow the same pattern:

1. `lockForUpdate()` on the reservation row.
2. Re-read status inside the transaction.
3. Only proceed if status is still `Pending`.

This prevents double-processing if two jobs (e.g., `ProcessPaymentJob` and `ExpireReservationsJob`) race to handle the same reservation.

---

## Queue Architecture

### ProcessPaymentJob

Dispatched when a fan hits `POST /reservations/{id}/pay`. It simulates a payment gateway with an 80% success rate:

- **Success** → calls `ReservationService::confirm()`, which creates `Ticket` records and transitions the reservation to `Confirmed`.
- **Failure** → calls `ReservationService::cancel()`, which restores `available_count` and transitions to `Cancelled`.
- **Expired** → if `expires_at` is past when the job runs, calls `expire()` instead.

Implements `ShouldBeUnique` with `uniqueId()` returning the reservation ID. A second `pay` request for the same reservation while a job is already queued is silently deduplicated — the job won't be pushed twice.

```
uniqueFor = 600 seconds  (matches the 10-minute reservation window)
```

### ExpireReservationsJob

Scheduled every minute via `routes/console.php`:

```php
Schedule::job(new ExpireReservationsJob())->everyMinute();
```

Fetches all `Pending` reservations where `expires_at < now()` and calls `expire()` on each, restoring seats to the category. Implements `ShouldBeUnique` globally (no `uniqueId`) so overlapping sweeps are prevented if the job takes longer than one minute.

```
uniqueFor = 120 seconds
```

### Job Deduplication Summary

| Job                    | Unique scope           | uniqueFor  |
|------------------------|------------------------|------------|
| `ProcessPaymentJob`    | per reservation ID     | 600s       |
| `ExpireReservationsJob`| global (one at a time) | 120s       |

---

## Known Limitations

### Payment Gateway is Simulated

`ProcessPaymentJob` uses `random_int(1, 100) <= 80` to simulate an 80% success rate. A real implementation would call an external payment provider, handle webhooks, and implement retry logic with exponential backoff.

### No Idempotency on Pay

`POST /reservations/{id}/pay` does not accept a client-supplied idempotency key. If a network timeout causes the client to retry, a second job may be dispatched. `ShouldBeUnique` mitigates this during the lock window, but once the lock expires the endpoint becomes callable again.

### Reservation Expiry is Eventually Consistent

`ExpireReservationsJob` runs every minute. A reservation technically stays "reservable" (blocking a seat) for up to 59 extra seconds after its `expires_at`. Under high load, if the queue is backed up, this window widens further.

### Max 4 Tickets Per Reservation

The `StoreReservationRequest` enforces `max:4`. There is no mechanism for a single user to hold more than 4 tickets for the same category across multiple reservations — this is not validated.

### No Soft Deletes

Cancelled and expired reservations are kept in the database with a `status` column rather than being soft-deleted. This is intentional for audit purposes but means the `reservations` table grows unboundedly and will need archiving in production.
