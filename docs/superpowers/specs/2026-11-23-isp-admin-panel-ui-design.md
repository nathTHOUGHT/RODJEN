# ISP Admin Panel UI Foundation

## Intent

Create the visual foundation for an ISP administration panel from the supplied screenshots. This phase exists so later screenshots and requirements can be added without repeatedly rebuilding the navigation, page shell, spacing, or common panel patterns.

The result is a frontend-only baseline inside the existing Laravel 13 Livewire starter. It should look coherent and professional, but it is not intended to reproduce the screenshots pixel-for-pixel.

## Goals

- Establish one responsive authenticated application shell with a text-based brand area, sidebar, top bar, content header, and footer.
- Restyle the existing login page to use the same restrained visual language while preserving the starter's existing Fortify login behavior.
- Add frontend page foundations for the screens shown in the references:
  - dashboard
  - add subscriber
  - subscriber records
  - products and services
  - policy and conditions
  - payment history
  - expenses
- Create reusable view components for page headings, panels, filters, empty tables, form sections, and chart placeholders.
- Keep every layout usable on desktop, tablet, and mobile.
- Make it easy to replace empty states with Livewire data and actions in a later backend phase.

## Non-goals

- No new database tables, migrations, Eloquent models, seeders, factories, policies, controllers, services, or API integrations.
- No subscriber, product, policy, payment, expense, billing, MikroTik, PON, SMS, or payment-provider business logic.
- No functional create, update, delete, import, export, chart aggregation, search, filtering, or pagination behavior.
- No copied customer records, balances, prices, dates, statistics, or other exact data from the screenshots.
- No image, logo, avatar, decorative illustration, or icon assets.
- No attempt to implement every sidebar module before the user supplies its page references.

## Anti-goals

- Do not create a fragile fixed-width desktop clone.
- Do not imply that disabled or placeholder controls already perform real operations.
- Do not bury future domain logic in Blade templates.
- Do not add a JavaScript charting or UI dependency for empty visual placeholders.
- Do not modify or remove the existing authentication, security settings, or account-management behavior merely to match a screenshot.

## Constraints

- Work inside the current repository and follow its Livewire 4, Flux UI Free, Tailwind CSS 4, and Blade conventions.
- Reuse the current Fortify login route and validation contract; this UI phase may restyle it but must not replace authentication logic.
- Use accessible labels, visible focus states, readable contrast, semantic tables, and clear empty-state copy.
- Use text and simple CSS shapes only. Navigation and controls must not depend on icons.
- The existing light/dark appearance mechanism should remain functional.
- Do not install new Composer or npm packages.

## Approaches Considered

### 1. Pixel-oriented desktop replica

This would mimic the screenshots closely with fixed measurements and dense legacy-style tables. It was rejected because the user does not need exact imagery, icons, records, or pixel-level reproduction, and the approach would be difficult to extend responsively.

### 2. Reusable responsive UI foundation - selected

Build the recognizable structure of the references with reusable Blade/Flux primitives and Tailwind styling. Pages will contain realistic headings, fields, column definitions, filters, action areas, and honest empty states without domain data or behavior. This gives the user a stable base for adding more screenshot-driven modules later.

### 3. Standalone static mockup files

This would keep the mockups separate from Laravel and minimize route work. It was rejected because it would duplicate the existing application shell and make later Livewire/backend integration needlessly expensive.

## Selected Page Structure

The screenshots supplied in the conversation are visual references for the page families below. This written structure is authoritative for the first pass: it intentionally captures the main regions, field labels, and table headings without requiring exact measurements, copied content, or every secondary control visible in the references.

### Login

- Split or centered authentication panel using CSS color blocks rather than an image.
- Application name rendered as text.
- Existing email, password, remember-me, validation, password-reset, passkey, and submission behavior remains available where the starter already exposes it.
- No copied email address, logo, terms statement, vendor branding, or copyright from the reference.

### Authenticated shell

- Persistent desktop sidebar and collapsible mobile navigation.
- Text-only application name and environment/site selector placeholder.
- Sidebar grouped into Dashboard, Subscriptions, Transactions, Operations, Settings, and Links.
- Only pages supported by current references receive active routes in this phase. Future modules may appear as clearly disabled labels or may be omitted until their screenshots arrive.
- Top bar contains a simple page context area and existing authenticated account/logout controls without decorative icons.
- Main content uses a subdued background with bordered white/dark panels.

### Dashboard

- Welcome/title panel.
- A month-grid calendar foundation rendered without live event data, with neutral month/year selectors.
- A compact date/time display area may be present as static visual structure; it must not add a live clock implementation in this phase.
- Summary-panel slots with neutral empty states rather than invented totals.
- No real-time clock or chart library.

### Add subscriber

- General profile and network profile form sections based on the visible field groups.
- General profile fields: account number, first name, last name, billing address, area, contact number, email address, coordinates, subscription date, credit limit, remarks, external URL, and image URL.
- Network profile fields: OLT device, PON port, NAP device, NAP port, ONU name, caller ID or PPPoE identifier, MAC address, and remarks.
- Fields are visually complete and labelled but do not submit or persist.
- Primary action is visibly marked unavailable for the frontend-only phase.
- Responsive grid collapses to a single column on narrow screens.

### Subscriber records

- Page-level area, status, due-date range, and search controls as visual placeholders.
- Table headers representing account, recurring dates, details, subscription, service, status, balance, and remarks.
- One full-width `No subscriber records yet` empty state instead of copied rows.
- Pagination area is a non-interactive placeholder.

### Products and services

- Add-action location, search field, and table shell.
- Columns for product, profile group, transaction type, price, and usage/account count.
- Empty state instead of plan records or prices.

### Policy and conditions

- Action area and search field.
- Table shell for policy name and PPPoE/hotspot/IPoE actions and conditions.
- Empty state instead of copied policy rules.

### Payment history

- Chart placeholder with labelled monthly/weekly display controls that do not aggregate data.
- Visual year, month, posting-status, search, and transaction-type controls.
- Table shell for timestamp, account details, transaction, receipt information, reference, and updated-by fields.
- Empty state instead of copied receipts.

### Expenses

- Expense chart placeholder with visual year, month, and search controls.
- Add-action location and table shell for timestamp, invoice, supplier/description, amount, and updated-by fields.
- Empty state instead of copied expenses.

## Reusable UI Pieces

- Application shell and grouped text navigation.
- Page heading and breadcrumb line.
- Panel/card wrapper with optional title and action slot.
- Filter bar with responsive wrapping.
- Form section and responsive field grid.
- Empty table component accepting column definitions and empty-state text.
- CSS-only chart placeholder.
- Frontend-only status notice for controls that intentionally have no behavior.

Components should be extracted only where they remove meaningful duplication across these pages. Existing Flux components should be used when they fit the free edition; ordinary Blade markup is acceptable for specialized table and chart placeholders.

## Routing and State

- Add named authenticated and verified routes for the new page views, following the project's existing Livewire page convention.
- The pages may be view-only Livewire single-file components with no mutable component state.
- Keep all existing authentication and settings routes intact.
- No query parameters, persistence, session-backed filters, or fake client-side CRUD state will be introduced.

## Responsive and Accessibility Behavior

- Sidebar remains available as a mobile drawer/toggle using the existing shell behavior.
- Form grids collapse progressively from multiple columns to one.
- Wide tables use horizontal scrolling and retain clear headers.
- Controls have programmatic labels, keyboard focus styles, disabled-state explanations, and sufficient contrast.
- Layout must remain readable at common mobile, tablet, laptop, and wide-desktop widths.

## Verification

- Feature tests confirm the login page remains public and application pages remain protected by authentication and email verification.
- Authenticated smoke tests confirm every new named route renders successfully without requiring business records.
- Existing Fortify and settings tests must continue to pass.
- Run Pint/lint, the PHPStan fallback suitable for this Windows environment, the full Pest suite, and the Vite production build.
- Perform a rendered-page review at desktop and mobile widths when a local browser path is available.

## Builder Decisions Left Open

- Exact color token values, spacing increments, and component boundaries may follow the existing Tailwind/Flux conventions as long as the overall look remains restrained and close to the reference structure.
- The builder may omit unsupported future sidebar labels rather than creating dead links.
- The builder may choose a split or centered login composition based on which integrates most cleanly with the current auth layout, provided no image is required.

## Future Backend Boundary

Later phases can add models, migrations, validation, authorization, queries, Livewire state/actions, charts, and integrations behind these views. Empty states and disabled actions must make those insertion points obvious without committing this frontend phase to an unverified database design.
