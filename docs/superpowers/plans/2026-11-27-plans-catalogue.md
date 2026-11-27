# Plans Catalogue Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a persistent, card-based Plans catalogue with accessible create/edit/delete dialogs and the supplied sample plan.

**Architecture:** A focused `Plan` Eloquent model owns catalogue constants and display labels. `PlanController` handles index/search and validated CRUD under the existing `panel.access` route group, while the Blade view uses native dialogs and Alpine only for opening/closing behavior.

**Tech Stack:** Laravel 13, Eloquent, Blade, Alpine, Tailwind CSS, Pest 5, SQLite/MySQL-compatible migration

**Spec:** `docs/superpowers/specs/2026-11-27-plans-catalogue-design.md`

## Global Constraints

- Keep `/subscriptions/products`, `products.index`, and the `Products and services` heading compatible with current navigation tests.
- Persist only plan catalogue data; do not introduce subscriber, billing, inventory, or MikroTik integrations.
- Use native project dependencies and patterns; add no packages.
- New plans start with `usage_count = 0`; the supplied seed uses `106`.
- New and edited plans require an integer speed from `1` to `100000` Mbps; the supplied seed uses `45` and legacy null values remain readable.
- Use exact transaction codes `mrc`, `otc`, `pcf`, `mrd`, `mds` and profile values `default-encryption`, `pppoe-45mbit/649`.

## Review Focus

- Duplicate or blank plan names must be rejected without losing entered values.
- Unsupported profile/type values and negative or over-precision prices must be rejected.
- Missing, non-integer, non-positive, or above-limit speed values must be rejected on create and update.
- Update uniqueness must ignore the plan being edited but reject another plan's name.
- Search must escape user input through Eloquent bindings and produce a clear no-results state.
- Guest or unverified mutations must remain blocked by `panel.access`.

---

### Task 1: Plan persistence and sample seed

**Files:**
- Create: `app/Models/Plan.php`
- Create: `database/migrations/2026_11_27_000000_create_plans_table.php`
- Create: `database/migrations/2026_11_27_010000_add_speed_mbps_to_plans_table.php`
- Create: `database/seeders/PlanSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Test: `tests/Feature/Admin/PlanManagementTest.php`

**Interfaces:**
- Produces: `Plan::TRANSACTION_TYPES`, `Plan::PROFILE_GROUPS`, `Plan::transactionTypeLabel()`, fillable plan attributes, speed/usage integer casts, and an idempotent `PlanSeeder`.

- [ ] **Step 1: Write failing persistence and seeder tests**
  Assert the supplied sample fields, 45 Mbps speed, money value, usage count, display label, and idempotent second seed run.
- [ ] **Step 2: Run the focused test and verify it fails because the plans table/model do not exist**
  Run: `php artisan test tests/Feature/Admin/PlanManagementTest.php --filter=seed --no-ansi`
- [ ] **Step 3: Add the migration, model constants/casts/label method, and idempotent seeder**
- [ ] **Step 4: Run the focused test and verify it passes**

### Task 2: Validated CRUD and search

**Files:**
- Create: `app/Http/Controllers/PlanController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Admin/PlanManagementTest.php`

**Interfaces:**
- Consumes: Plan constants and route-model binding from Task 1.
- Produces: `products.index`, `products.store`, `products.update`, and `products.destroy`; index variables `plans`, `editingPlan`, `profileGroups`, and `transactionTypes`.

- [ ] **Step 1: Write failing tests for index/search, create, update, delete, validation, missing records, and blocked guest mutations**
- [ ] **Step 2: Run the test file and verify the expected missing-route/controller failures**
- [ ] **Step 3: Implement the controller and replace the view-only route with controller CRUD routes**
  Share one validation-rule builder; require speed from 1 to 100000 Mbps; use unique-name ignore on update; redirect with concise status messages.
- [ ] **Step 4: Run the focused test file and verify all controller tests pass**

### Task 3: Accessible modal and card-grid UI

**Files:**
- Modify: `resources/views/admin/products/index.blade.php`
- Modify: `tests/Feature/Admin/SubscriptionUiTest.php`
- Test: `tests/Feature/Admin/PlanManagementTest.php`

**Interfaces:**
- Consumes: controller view variables and `products.*` routes from Task 2.
- Produces: semantic plan cards with speed above price, search form, create/edit native dialogs with Speed above Price, delete confirmation, inline validation errors, and empty states.

- [ ] **Step 1: Replace the legacy table assertion with failing card/dialog assertions**
  Assert the supplied plan content, 45 Mbps display, Speed-before-Price ordering, all five type options, labelled dialog controls, action URLs, and absence of the legacy table copy.
- [ ] **Step 2: Run the UI tests and verify they fail against the placeholder view**
- [ ] **Step 3: Implement the responsive grid and dialogs using existing Tailwind/Alpine conventions**
- [ ] **Step 4: Run the focused Plan and Subscription UI tests and verify they pass**

### Task 4: Workspace migration, seed, and verification

**Files:**
- No additional source files.

**Interfaces:**
- Consumes: all prior tasks.
- Produces: migrated local catalogue with the supplied sample and verified application behavior.

- [ ] **Step 1: Run `php artisan migrate --force`**
- [ ] **Step 2: Run `php artisan db:seed --class=PlanSeeder --force`**
- [ ] **Step 3: Run formatting, static analysis, full tests, and frontend build**
  Run: `vendor/bin/pint --test`, `vendor/bin/phpstan analyse`, `php artisan test --no-ansi`, and `npm run build`.
- [ ] **Step 4: Inspect `git diff --check` and `git status --short` for unintended changes**
