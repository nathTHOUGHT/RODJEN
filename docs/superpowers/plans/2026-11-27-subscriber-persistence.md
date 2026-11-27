# Subscriber Persistence Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Save subscribers from the existing form and display persisted records and details.

**Architecture:** A `Subscriber` owns one initial `Subscription`, which owns one `NetworkAccount`; the subscription optionally references an existing monthly `Plan`. A controller and form request provide the HTTP boundary, while Blade views remain presentation-only.

**Tech Stack:** Laravel 13, PHP 8.3, Eloquent, Blade, Pest, SQLite/MySQL-compatible migrations.

**Spec:** `docs/superpowers/specs/2026-11-27-subscriber-persistence-design.md`

## Global Constraints

- Preserve the current subscriber page styling and route group protected by `panel.access`.
- Preserve leading zeroes in account numbers and identifiers.
- Never render, serialize, log, or flash the PPPoE password.
- Do not implement billing, editing, deletion, or external MikroTik calls.

## Review Focus

- Duplicate account numbers and PPPoE usernames must return validation errors without partial rows.
- Invalid or non-MRC plan IDs must be rejected.
- Optional fields must accept null without breaking records or details.
- A failed related-row insert must roll back the subscriber and plan usage increment.
- Guests and unverified users must not create or inspect subscriber records.

---

### Task 1: Subscriber persistence boundary

**Files:**
- Create: `tests/Feature/Admin/SubscriberManagementTest.php`
- Create: `database/migrations/2026_11_27_020000_create_subscribers_table.php`
- Create: `database/migrations/2026_11_27_021000_create_subscriptions_table.php`
- Create: `database/migrations/2026_11_27_022000_create_network_accounts_table.php`
- Create: `app/Models/Subscriber.php`
- Create: `app/Models/Subscription.php`
- Create: `app/Models/NetworkAccount.php`
- Create: `app/Http/Requests/StoreSubscriberRequest.php`
- Create: `app/Http/Controllers/SubscriberController.php`
- Modify: `routes/web.php`
- Modify: `bootstrap/app.php`

**Interfaces:**
- Consumes: existing `Plan` records where `transaction_type = mrc` and the `panel.access` middleware.
- Produces: `SubscriberController::{index,create,store,show}`, model relationships, and `subscribers.store` / parameterized `subscribers.details` routes.

- [ ] Write feature tests proving valid creation preserves a leading-zero account number, creates all three rows, encrypts the PPPoE password, increments plan usage, and redirects to details.
- [ ] Run the focused test and confirm it fails because the store route/tables do not exist.
- [ ] Write validation tests for duplicate identifiers, non-MRC plans, invalid dates/URLs/billing day, and transaction rollback.
- [ ] Implement the migrations, models, form request, controller transaction, routes, and sensitive-input middleware configuration.
- [ ] Run `php artisan test --compact tests/Feature/Admin/SubscriberManagementTest.php` and confirm the persistence tests pass.

### Task 2: Database-backed Add Subscriber form

**Files:**
- Modify: `resources/views/admin/subscribers/create.blade.php`
- Modify: `resources/views/admin/subscribers/fields.blade.php`
- Modify: `tests/Feature/Admin/SubscriptionUiTest.php`

**Interfaces:**
- Consumes: `SubscriberController::create()` variables `plans` and validation error bag.
- Produces: CSRF-protected POST payload accepted by `StoreSubscriberRequest`.

- [ ] Replace the preview-only assertions with tests for the real form action, names, plan select, safe old input, validation errors, and enabled submit button.
- [ ] Run the focused UI test and confirm it fails against the preview-only form.
- [ ] Convert the wrapper to a POST form, add semantic names/constraints/errors, retain existing IDs, and never repopulate `pppoe_password`.
- [ ] Run the focused tests and confirm they pass.

### Task 3: Persisted records and details

**Files:**
- Modify: `resources/views/admin/subscribers/index.blade.php`
- Modify: `resources/views/admin/subscribers/show.blade.php`
- Modify: `tests/Feature/Admin/SubscriptionUiTest.php`
- Modify: `tests/Feature/Admin/SubscriberDetailsUiTest.php`
- Modify: `tests/Feature/Admin/AdminRoutesTest.php`
- Delete: `resources/data/sample-subscriber.php`

**Interfaces:**
- Consumes: paginated `subscribers`, query filters, one `subscriber`, and label-keyed detail arrays from the controller.
- Produces: real record rows, subscriber-specific links, honest empty states, and persisted detail panels.

- [ ] Add tests proving records list multiple stored subscribers, filters work, details show only the selected subscriber, missing IDs return 404 after access checks, and passwords never appear.
- [ ] Run the focused tests and confirm they fail on the sample-only views.
- [ ] Replace the sample row/data with collection rendering, filters, pagination, empty state, and persisted detail arrays.
- [ ] Update parameterized route tests and remove the unused sample-data file.
- [ ] Run all subscriber/admin tests and confirm they pass.

### Task 4: Quality verification

**Files:**
- Verify all changed PHP, Blade, routes, migrations, and tests.

**Interfaces:**
- Consumes: completed feature.
- Produces: verified migration, formatting, build, and test evidence.

- [ ] Run `vendor/bin/pint --dirty --test`.
- [ ] Run `php artisan migrate:fresh --seed --no-interaction` only against the test database or an isolated temporary database.
- [ ] Run `php artisan view:cache` and `npm run build`.
- [ ] Run the complete `php artisan test --compact` suite and report any pre-existing failure separately.
- [ ] Review `git diff --check`, sensitive-field handling, and the final changed-file list.

Implementation is executed directly in the current workspace as requested; commits are deferred unless explicitly requested.
