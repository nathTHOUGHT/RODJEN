# ISP Admin Panel UI Foundation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (- [ ]) syntax for tracking.

**Goal:** Build a responsive, frontend-only ISP administration panel foundation for every supplied screenshot while preserving the starter application's existing authentication behavior.

**Architecture:** Keep all business pages as server-rendered Blade views behind one focused access middleware that accepts either a local database-free preview session or a real authenticated, verified Fortify user. Use one small preview-session controller plus configuration for the requested local admin/admin flow; business pages still have no models, persistence, fake datasets, or actions. Reuse small admin Blade components for headings, panels, empty tables, notices, and CSS-only charts; use Flux only where its free components fit without introducing decorative icons.

**Tech Stack:** Laravel 13, Blade, Livewire 4 runtime, Flux UI Free 2, Tailwind CSS 4, Alpine supplied by Livewire/Flux, Pest 4, Vite.

**Spec:** docs/superpowers/specs/2026-11-23-isp-admin-panel-ui-design.md

## Global Constraints

- Do not create database tables, migrations, Eloquent models, seeders, factories, policies, domain services, or API integrations. The only controller added in this phase is the focused local preview-session controller.
- Do not add business logic for subscribers, billing, products, policies, payments, expenses, logs, MikroTik, messaging, scheduling, or integrations.
- Do not copy customer records, prices, balances, dates, messages, credentials, statistics, or other exact data from the screenshots.
- Do not add images, logos, avatars, decorative illustrations, or icons; use text and simple CSS shapes.
- Preserve Fortify authentication, verification, password reset, two-factor authentication, passkeys, settings routes, CSRF protection, and logout.
- Preview access must use a server-side session, rate-limited login endpoint, session regeneration, generic credential errors, and configuration values; never implement it as client-side authentication or enable it by default in production.
- Do not install Composer or npm packages.
- Keep light/dark appearance support and responsive behavior.
- Page controls may open and close presentational modals, but no create, update, delete, import, export, connection, sync, or save operation may run.

## Review Focus

1. Guests and unverified users must not reach any new administration route, while a valid local preview session must work without a user record; Tasks 1 and 2 test all three states.
2. Navigation must remain usable without icons and must mark nested sections clearly; Tasks 1 and 2 test text navigation and current-page headings.
3. Frontend-only controls must never imply or trigger persistence; Tasks 3 through 7 test disabled action markers and the absence of business forms.
4. Wide tables and dense forms must remain usable at narrow widths; Tasks 3 through 8 assert the shared horizontal-scroll and responsive-grid hooks.
5. Supplied screenshots contain real-looking data and secrets that must not leak into the new UI; Tasks 3 through 8 assert explicit empty states, blank sensitive fields, and no image/SVG markup.

---

### Task 1: Text-only authentication and application shell

**Files:**
- Create: app/Http/Controllers/PreviewSessionController.php
- Create: app/Http/Middleware/EnsurePanelAccess.php
- Create: config/preview.php
- Create: tests/Feature/Auth/PreviewAuthenticationTest.php
- Modify: .env.example
- Modify: bootstrap/app.php
- Modify: routes/web.php
- Modify: resources/css/app.css
- Modify: resources/views/layouts/auth/simple.blade.php
- Modify: resources/views/pages/auth/login.blade.php
- Modify: resources/views/components/passkey-verify.blade.php
- Modify: resources/views/components/app-logo.blade.php
- Modify: resources/views/components/desktop-user-menu.blade.php
- Modify: resources/views/layouts/app/sidebar.blade.php
- Modify: resources/views/dashboard.blade.php
- Modify: tests/Feature/Auth/AuthenticationTest.php
- Modify: tests/Feature/DashboardTest.php

**Interfaces:**
- Consumes: Existing Fortify named routes, x-layouts::auth, x-layouts::app, optional authenticated User name/email, Flux scripts and appearance state.
- Produces: POST preview.login and POST preview.logout; panel.access middleware; preview.user session payload; a text-only responsive auth layout and panel shell whose slot remains the content boundary for every later page.

- [ ] **Step 1: Add failing preview-authentication tests**

Create PreviewAuthenticationTest.php. With preview.enabled=true and configured admin/admin credentials, assert: the login page renders Username and Password without querying users; valid credentials redirect to dashboard, regenerate the session identifier, and store preview.user; invalid credentials redirect back with a generic username error and no preview session; dashboard redirects a guest to login; preview logout clears the session and redirects to login; preview login returns 404 when disabled. Use the real session and routes, not mocks.

- [ ] **Step 2: Run the preview-authentication tests and confirm failure**

Run: php artisan test --compact tests/Feature/Auth/PreviewAuthenticationTest.php

Expected: FAIL because preview configuration, endpoints, middleware, and login mode do not exist.

- [ ] **Step 3: Implement secure local preview access**

Create config/preview.php with enabled, username, and password values read only from environment variables. Document disabled example configuration in .env.example; set the current untracked local .env to enable admin/admin for this workspace. PreviewSessionController validates username/password, rejects disabled access with 404, compares configured strings without logging them, applies a generic failure message, regenerates the session on success, and invalidates/regenerates the token on logout. Apply throttle:5,1 to preview.login.

EnsurePanelAccess allows a preview.user session, allows a real authenticated verified Fortify user, redirects an unverified real user to verification.notice, and redirects all other requests to login. Register panel.access in bootstrap/app.php. Redirect / to login, protect dashboard with panel.access, and keep Fortify's existing routes available.

- [ ] **Step 4: Add failing presentation tests**

Add tests named 'login screen uses the text-only admin presentation' and 'preview session can visit the text-only admin shell'. Assert the preview login response contains 'Welcome back', Username, Password, and data-test="login-button"; assert a preview-authenticated dashboard contains 'Administration Panel', Dashboard, and data-test="logout-button". Both responses must exclude literal <img and <svg markup. Keep the existing Fortify email/password authentication tests green by allowing real verified users through panel.access.

~~~php
$response
    ->assertSee('Welcome back')
    ->assertSee('data-test="login-button"', false)
    ->assertDontSee('<img', false)
    ->assertDontSee('<svg', false);
~~~

- [ ] **Step 5: Run the focused presentation tests and confirm failure**

Run: php artisan test --compact tests/Feature/Auth/AuthenticationTest.php tests/Feature/DashboardTest.php

Expected: FAIL because the starter shell still renders logo/icon SVG markup and lacks the new copy.

- [ ] **Step 6: Implement the text-only layouts**

Define restrained admin color tokens in app.css. Replace the auth logo area with a CSS-only brand panel. When preview access is enabled, render the Username/Password form posting to preview.login; otherwise retain Fortify email/password, reset, registration, and passkey behavior. Remove the passkey icon attribute and password eye control. Replace the application logo, desktop account menu, mobile toggle, and authenticated header with text-only equivalents. Resolve the displayed name and logout route from either the real user or preview.user while retaining CSRF, data-test hooks, Flux appearance, toast, and mobile sidebar dispatch.

- [ ] **Step 7: Replace the dashboard placeholder pattern**

Render a minimal page heading and neutral frontend-foundation notice without using x-placeholder-pattern, images, or SVG markup. Later Task 3 replaces this temporary dashboard body.

- [ ] **Step 8: Run the focused tests**

Run: php artisan test --compact tests/Feature/Auth/PreviewAuthenticationTest.php tests/Feature/Auth/AuthenticationTest.php tests/Feature/DashboardTest.php

Expected: PASS, including all pre-existing authentication assertions.

- [ ] **Step 9: Commit**

~~~powershell
git add .env.example app/Http/Controllers/PreviewSessionController.php app/Http/Middleware/EnsurePanelAccess.php bootstrap/app.php config/preview.php routes/web.php resources/css/app.css resources/views/layouts/auth/simple.blade.php resources/views/pages/auth/login.blade.php resources/views/components/passkey-verify.blade.php resources/views/components/app-logo.blade.php resources/views/components/desktop-user-menu.blade.php resources/views/layouts/app/sidebar.blade.php resources/views/dashboard.blade.php tests/Feature/Auth/PreviewAuthenticationTest.php tests/Feature/Auth/AuthenticationTest.php tests/Feature/DashboardTest.php
git commit -m "feat: add local preview login and admin shell"
~~~

### Task 2: Shared admin components, routes, and navigation

**Files:**
- Create: resources/views/components/admin/page-heading.blade.php
- Create: resources/views/components/admin/panel.blade.php
- Create: resources/views/components/admin/empty-table.blade.php
- Create: resources/views/components/admin/chart-placeholder.blade.php
- Create: resources/views/components/admin/frontend-notice.blade.php
- Create: resources/views/admin/subscribers/create.blade.php
- Create: resources/views/admin/subscribers/index.blade.php
- Create: resources/views/admin/subscribers/show.blade.php
- Create: resources/views/admin/products/index.blade.php
- Create: resources/views/admin/policies/index.blade.php
- Create: resources/views/admin/reports/index.blade.php
- Create: resources/views/admin/reports/daily.blade.php
- Create: resources/views/admin/reports/transactions.blade.php
- Create: resources/views/admin/reports/expenses.blade.php
- Create: resources/views/admin/logs/system.blade.php
- Create: resources/views/admin/logs/activity.blade.php
- Create: resources/views/admin/settings/router/index.blade.php
- Create: resources/views/admin/settings/router/backups.blade.php
- Create: resources/views/admin/settings/users/index.blade.php
- Create: resources/views/admin/integrations/messaging.blade.php
- Create: resources/views/admin/integrations/scheduler.blade.php
- Create: resources/views/admin/integrations/settings.blade.php
- Create: tests/Feature/Admin/AdminRoutesTest.php
- Modify: routes/web.php
- Modify: resources/views/layouts/app/sidebar.blade.php

**Interfaces:**
- Produces: x-admin.page-heading(title, section), x-admin.panel(title, optional actions slot), x-admin.empty-table(columns: array, message: string), x-admin.chart-placeholder(title, subtitle), and x-admin.frontend-notice.
- Produces named routes: subscribers.create, subscribers.index, subscribers.details, products.index, policies.index, reports.index, reports.daily, reports.transactions, reports.expenses, logs.system, logs.activity, router.index, router.backups, staff.index, integrations.messaging, integrations.scheduler, integrations.settings.
- Consumes: The Task 1 x-layouts::app shell, text navigation patterns, and panel.access middleware.

- [ ] **Step 1: Write failing route-access tests**

Create a route/heading matrix and test every route as guest, local preview session, authenticated-unverified, and authenticated-verified. Guests redirect to login; preview sessions and verified users receive 200 with the expected heading; unverified real users redirect to verification.notice. Include unique headings for all seventeen pages.

~~~php
test('admin pages require a verified user and render their heading', function (string $route, string $heading) {
    $this->get(route($route))->assertRedirect(route('login'));

    $unverified = User::factory()->unverified()->create();
    $this->actingAs($unverified)->get(route($route))->assertRedirect(route('verification.notice'));

    $verified = User::factory()->create();
    $this->actingAs($verified)->get(route($route))->assertOk()->assertSee($heading);
})->with([
    ['subscribers.create', 'Add subscriber'],
    ['subscribers.index', 'Subscriber records'],
    ['subscribers.details', 'Subscriber details'],
    ['reports.index', 'Reports'],
    ['reports.daily', 'Daily accounting report'],
    ['reports.transactions', 'Payment and transaction history'],
    ['reports.expenses', 'Expenses'],
    // Include every interface route listed above.
]);
~~~

- [ ] **Step 2: Run the route tests and confirm failure**

Run: php artisan test --compact tests/Feature/Admin/AdminRoutesTest.php

Expected: FAIL because the named routes and views do not exist.

- [ ] **Step 3: Create shared admin primitives**

Implement the five components with explicit props. empty-table renders a semantic table, a data-table-scroll marker on an overflow-x-auto wrapper, all supplied column headings, and one colspan empty row. chart-placeholder renders CSS grid lines and neutral labels without SVG/canvas. frontend-notice states that the screen is a frontend foundation and actions are unavailable.

- [ ] **Step 4: Add protected routes and initial page shells**

Add all named Route::view declarations to the panel.access group. Each initial view uses x-layouts::app, x-admin.page-heading, and x-admin.frontend-notice, and contains no models, queries, or mutable state.

- [ ] **Step 5: Expand the text-only sidebar**

Add route-aware text links grouped under Dashboard, Subscriptions, Reports, API Integration, Settings, and Logs. Reports contains Overview, Daily accounting report, Payment and transaction history, and Expenses; do not render separate Transactions or Expenses top-level sections. Keep unsupported future modules out of the navigation. Do not add icon attributes, SVG, images, or dead links.

- [ ] **Step 6: Run the route and shell tests**

Run: php artisan test --compact tests/Feature/Admin/AdminRoutesTest.php tests/Feature/DashboardTest.php

Expected: PASS for every access state, route heading, and existing shell assertion.

- [ ] **Step 7: Commit**

~~~powershell
git add routes/web.php resources/views/components/admin resources/views/admin resources/views/layouts/app/sidebar.blade.php tests/Feature/Admin/AdminRoutesTest.php
git commit -m "feat: scaffold admin UI routes"
~~~

### Task 3: Dashboard and subscription panels

**Files:**
- Modify: resources/views/dashboard.blade.php
- Modify: resources/views/admin/subscribers/create.blade.php
- Modify: resources/views/admin/subscribers/index.blade.php
- Modify: resources/views/admin/products/index.blade.php
- Modify: resources/views/admin/policies/index.blade.php
- Create: tests/Feature/Admin/SubscriptionUiTest.php

**Interfaces:**
- Consumes: All Task 2 admin components and named routes.
- Produces: Static dashboard/calendar, subscriber form foundation, and empty subscriber/product/policy tables.

- [ ] **Step 1: Write failing UI-contract tests**

Test the dashboard for Month overview, Sun through Sat headings, and No activity scheduled. Test Add subscriber for both form-section headings and every field named in the spec. Test records, products, and policies for their required column headings and exact empty-state copy. Assert Add subscriber contains a data-ui-only-form container plus a data-test="subscriber-create-disabled" control marked disabled and type="button"; do not use a generic no-<form assertion because the shared shell intentionally contains the secure logout form.

- [ ] **Step 2: Run the tests and confirm failure**

Run: php artisan test --compact tests/Feature/Admin/SubscriptionUiTest.php

Expected: FAIL because the pages contain only initial shells.

- [ ] **Step 3: Implement the dashboard foundation**

Build a responsive title/welcome panel, neutral month/year selectors, a seven-column month grid, and empty summary slots. Use CSS and text only; do not calculate the current date or render activity data.

- [ ] **Step 4: Implement the Add subscriber foundation**

Render labelled General profile and Network profile controls from the spec in responsive grids. Inputs remain blank and outside any mutation form. The final Create control is a disabled type="button" with data-test="subscriber-create-disabled".

- [ ] **Step 5: Implement subscriber, product, and policy tables**

Use filter controls as unbound visual inputs and x-admin.empty-table for every specified column set. Use No subscriber records yet, No products or services yet, and No policies or conditions yet as the empty messages.

- [ ] **Step 6: Run focused tests**

Run: php artisan test --compact tests/Feature/Admin/SubscriptionUiTest.php tests/Feature/Admin/AdminRoutesTest.php

Expected: PASS.

- [ ] **Step 7: Commit**

~~~powershell
git add resources/views/dashboard.blade.php resources/views/admin/subscribers resources/views/admin/products resources/views/admin/policies tests/Feature/Admin/SubscriptionUiTest.php
git commit -m "feat: add subscription UI panels"
~~~

### Task 4: Subscriber details foundation

**Files:**
- Modify: resources/views/admin/subscribers/show.blade.php
- Create: tests/Feature/Admin/SubscriberDetailsUiTest.php

**Interfaces:**
- Consumes: Task 2 admin components, the subscribers.details named route, and Flux modal/trigger/close components.
- Produces: A static subscriber details page with overview panels, CSS-only map placeholders, empty histories, and five presentational modals that cannot mutate data.

- [ ] **Step 1: Write the failing subscriber-details contract tests**

Assert the response contains Account details, Service details, Coverage map, Customer location, Network path, PON details, MikroTik parameters, Transaction summary, Transactions, System logs, and Activity logs. Assert the account/action area exposes descriptive text labels for Archive, Message, Support ticket, Job order, Transaction, Billing status, Service state, and Override date without img/svg markup.

Assert modal content for Remove account, Account settings, Deactivate billing, Override date, and Transaction details. Account settings must expose service type, username, blank password, subscription, local/remote address, caller ID, and credit limit. Transaction details must expose transaction date, current balance, transaction type, credit date, description, amount, payment mode, and optional reference. Every Confirm, Update, or Submit control is type="button", disabled, and has a page-specific data-test marker.

Assert display rows use only neutral empty markers such as Not available or an em dash, credential fields have blank values, and all history tables show empty-state copy. Do not place any screenshot account number, subscriber identity, contact data, network address, credential, plan, balance, or log text in either production or test files.

- [ ] **Step 2: Run the test and confirm failure**

Run: php artisan test --compact tests/Feature/Admin/SubscriberDetailsUiTest.php

Expected: FAIL because subscribers/show contains only the initial shell.

- [ ] **Step 3: Implement account and service overview panels**

Use a CSS-only profile placeholder, blank labelled account rows, a blank message-template select, and wrapped text actions. Render neutral service/balance fields and recurring/account-access labels with em-dash or Not available values rather than numeric examples. Only controls that open presentational modals may be enabled.

- [ ] **Step 4: Implement network, transaction, and log placeholders**

Render three CSS-only map placeholders without iframe/canvas/provider assets. Add PON and MikroTik parameter label grids, four neutral transaction summary cards, an empty transaction table, and subscriber-specific empty system/activity tables.

- [ ] **Step 5: Implement the five presentational modals**

Use Flux modals with closable=false and text Close/Cancel controls. Keep all fields blank and unbound. Remove account, Account settings, Deactivate billing, Override date, and Transaction details mutation controls remain disabled type="button" elements with stable data-test markers; do not add forms, wire actions, routes, or confirmation logic.

- [ ] **Step 6: Run focused tests**

Run: php artisan test --compact tests/Feature/Admin/SubscriberDetailsUiTest.php tests/Feature/Admin/SubscriptionUiTest.php tests/Feature/Admin/AdminRoutesTest.php

Expected: PASS.

- [ ] **Step 7: Commit**

~~~powershell
git add resources/views/admin/subscribers/show.blade.php tests/Feature/Admin/SubscriberDetailsUiTest.php
git commit -m "feat: add subscriber details UI"
~~~

### Task 5: Reports center and log panels

**Files:**
- Modify: resources/views/admin/reports/index.blade.php
- Modify: resources/views/admin/reports/daily.blade.php
- Modify: resources/views/admin/reports/transactions.blade.php
- Modify: resources/views/admin/reports/expenses.blade.php
- Modify: resources/views/admin/logs/system.blade.php
- Modify: resources/views/admin/logs/activity.blade.php
- Create: tests/Feature/Admin/ReportingUiTest.php

**Interfaces:**
- Consumes: x-admin.panel, x-admin.empty-table, x-admin.chart-placeholder, and x-admin.frontend-notice.
- Produces: A unified Reports landing page, a static daily accounting report preview, and static payment, expense, system-log, and activity-log reporting panels.

- [ ] **Step 1: Write failing report-panel tests**

Assert the Reports landing page contains text links to Daily accounting report, Payment and transaction history, and Expenses and contains no invented totals. Assert Daily accounting report contains a blank date input, a Staff/User select with the multiple attribute and no invented user options, disabled Generate preview/Print/Export controls, and the Accounting Daily Report, Bill Collection Summary, Other Cash Collection Summary, Expenses Breakdown, Cash Status, Remarks, Prepared by, Checked by, and Approved by labels. For each other report/log route, assert the required chart title when applicable, filter labels, table headings, data-table-scroll hook, and page-specific empty message. Assert the responses contain no copied names, company, date, amount, receipt, balance, log, or activity rows and no img/svg markup.

- [ ] **Step 2: Run the tests and confirm failure**

Run: php artisan test --compact tests/Feature/Admin/ReportingUiTest.php

Expected: FAIL because the report shells lack their controls and tables.

- [ ] **Step 3: Implement the unified Reports section**

Build a Reports landing page with text-only cards linking to the three available financial reports. Build a compact print-oriented Daily accounting report preview with a blank date field, empty native multiple-select for future staff options, helper text, disabled actions, blank header values, collection/expense/cash-status tables, remarks, and signature lines. Compose the Payment and transaction history and Expenses pages with CSS-only chart placeholders, monthly/weekly labels where specified, blank year/month/status/search controls, and empty tables with the spec's columns. Export and add controls are disabled text buttons with data-test hooks and no download/create behavior.

- [ ] **Step 4: Implement system and activity log panels**

Create the system-log filter/table foundation and the activity chart/filter/table foundation. Use No system logs yet and No activity notes yet empty messages.

- [ ] **Step 5: Run focused tests**

Run: php artisan test --compact tests/Feature/Admin/ReportingUiTest.php tests/Feature/Admin/AdminRoutesTest.php

Expected: PASS.

- [ ] **Step 6: Commit**

~~~powershell
git add resources/views/admin/reports resources/views/admin/logs tests/Feature/Admin/ReportingUiTest.php
git commit -m "feat: add reporting UI panels"
~~~

### Task 6: Router and staff settings panels

**Files:**
- Modify: resources/views/admin/settings/router/index.blade.php
- Modify: resources/views/admin/settings/router/backups.blade.php
- Modify: resources/views/admin/settings/users/index.blade.php
- Create: tests/Feature/Admin/SettingsUiTest.php

**Interfaces:**
- Consumes: Task 2 components and Flux modal/trigger/close components.
- Produces: Presentational router configuration and staff-user modals that can open/close but cannot submit.

- [ ] **Step 1: Write failing settings-panel tests**

Assert Router overview contains Router, Backup, Connection status, Primary connection, Board name, RouterOS version, and data-test hooks for disabled Sync Router and Save configuration controls. Assert the Edit router configuration modal includes primary/backup host and port, username, blank password input, disabled Save control, and no image/SVG. Assert Backup history and Staff users expose all required table columns and empty messages. Assert the Add user modal contains all specified fields and a disabled type="button".

- [ ] **Step 2: Run the tests and confirm failure**

Run: php artisan test --compact tests/Feature/Admin/SettingsUiTest.php

Expected: FAIL because settings pages are still shells.

- [ ] **Step 3: Implement router overview and modal**

Add Router/Backup text tabs, neutral status and metadata placeholders, disabled text controls, and a Flux configuration modal with blank inputs. Set the Flux modal's closable prop to false and provide a text Close control so it does not render a close icon. Do not use a form action or wire submission.

- [ ] **Step 4: Implement backup and staff tables**

Render empty backup history and staff-user tables. Add a presentational Flux Add user modal with closable=false, a text Close control, weekday checkboxes, time inputs, group selection, and blank identity/credential fields. Replace action icon columns with text-only disabled action labels.

- [ ] **Step 5: Run focused tests**

Run: php artisan test --compact tests/Feature/Admin/SettingsUiTest.php tests/Feature/Admin/AdminRoutesTest.php

Expected: PASS.

- [ ] **Step 6: Commit**

~~~powershell
git add resources/views/admin/settings tests/Feature/Admin/SettingsUiTest.php
git commit -m "feat: add settings UI panels"
~~~

### Task 7: Messaging, scheduler, and integration settings

**Files:**
- Modify: resources/views/admin/integrations/messaging.blade.php
- Modify: resources/views/admin/integrations/scheduler.blade.php
- Modify: resources/views/admin/integrations/settings.blade.php
- Create: tests/Feature/Admin/IntegrationUiTest.php

**Interfaces:**
- Consumes: Task 2 components and Flux modal/trigger/close components.
- Produces: Static messaging, scheduler, event, and integration configuration foundations with no provider calls or persisted secrets.

- [ ] **Step 1: Write failing integration-panel tests**

Assert Messaging contains Templates, Delivery history, Clients, Others, its table headings, and both empty states. Assert Scheduler contains Schedules, Event templates, Add schedule, Add event, all modal field groups, and disabled Submit controls. Assert Integration settings contains text headings for Chat, TR-069, Monitoring, Email, Mobile SMS, M360, Semaphore, Maya, Xendit, and REST API; secret fields must be type="password" with blank values. Assert all three responses exclude img/svg markup and copied messages/credentials.

- [ ] **Step 2: Run the tests and confirm failure**

Run: php artisan test --compact tests/Feature/Admin/IntegrationUiTest.php

Expected: FAIL because integration pages are still shells.

- [ ] **Step 3: Implement messaging foundations**

Render an empty template table and empty delivery-history table with visual tabs and filters. Use short text channel labels and disabled text action controls.

- [ ] **Step 4: Implement scheduler foundations and modals**

Render empty schedule/event tables. Add Flux Add schedule and Add event modals with closable=false, text Close controls, the spec's blank controls, concise placeholder-token guidance, and disabled type="button" submissions.

- [ ] **Step 5: Implement integration settings panels**

Render provider/category names as text-only panel headings with blank labelled inputs. Use password inputs for keys, secrets, passphrases, and passwords. Omit logos, vendor marketing copy, endpoints, and example credentials.

- [ ] **Step 6: Run focused tests**

Run: php artisan test --compact tests/Feature/Admin/IntegrationUiTest.php tests/Feature/Admin/AdminRoutesTest.php

Expected: PASS.

- [ ] **Step 7: Commit**

~~~powershell
git add resources/views/admin/integrations tests/Feature/Admin/IntegrationUiTest.php
git commit -m "feat: add integration UI panels"
~~~

### Task 8: Presentation constraints and full verification

**Files:**
- Create: tests/Feature/Admin/PresentationConstraintsTest.php
- Modify as needed: resources/css/app.css
- Modify as needed: resources/views/components/admin/*.blade.php
- Modify as needed: resources/views/admin/**/*.blade.php
- Modify as needed: resources/views/dashboard.blade.php
- Modify as needed: resources/views/layouts/app/sidebar.blade.php

**Interfaces:**
- Consumes: Every page and component from Tasks 1 through 7.
- Produces: Verified responsive hooks, accessible labels, honest disabled states, and a clean build across the whole UI foundation.

- [ ] **Step 1: Write cross-page constraint tests**

For the full route matrix, assert authenticated responses have no img/svg markup. For every table page, assert data-table-scroll and overflow-x-auto. For every frontend-only primary action, assert its data-test marker and disabled/type="button" contract. Assert sensitive integration inputs are blank password fields. Assert the existing logout POST form remains present and is the only intentional mutation form in the shared shell.

- [ ] **Step 2: Run the constraint tests and fix failures**

Run: php artisan test --compact tests/Feature/Admin/PresentationConstraintsTest.php

Expected: PASS after correcting any inconsistent component markup, missing labels, overflow hooks, or action states.

- [ ] **Step 3: Run formatting and whitespace checks**

Run: composer lint

Expected: PASS.

Run: git diff --check

Expected: no output.

- [ ] **Step 4: Run static analysis with the verified Windows fallback**

Run: php vendor/phpstan/phpstan/phpstan analyse --debug --no-progress --memory-limit=512M

Expected: 0 errors.

- [ ] **Step 5: Run the full test suite**

Run: php artisan test --compact

Expected: all tests pass.

- [ ] **Step 6: Build production assets**

Run: npm.cmd run build

Expected: Vite exits 0 and writes the production manifest/assets.

- [ ] **Step 7: Review routes and repository state**

Run: php artisan route:list --except-vendor

Expected: every new route is named and protected by auth and verified middleware.

Run: git status --short

Expected: only intended implementation and plan changes are present before the final commit.

- [ ] **Step 8: Commit**

~~~powershell
git add resources tests routes docs/superpowers/plans/2026-11-23-isp-admin-panel-ui-foundation.md
git commit -m "test: verify admin UI foundation"
~~~
