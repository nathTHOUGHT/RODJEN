# Plans Catalogue Design

## Intent

Turn Settings > Plans from a frontend-only empty table into a database-backed plan catalogue. The catalogue must remain focused on subscriber plans rather than becoming a generic products-and-services subsystem.

## Goals

- Preserve the existing `/subscriptions/products` URL, `products.*` route namespace, Settings tab, and `Products and services` page heading.
- Render plans as responsive grid cards rather than a table.
- Open an accessible native dialog when Add is selected, with Product Name, Profile, Type, Speed (Mbps), and Price fields.
- Support persisted create, edit, and delete actions with server-side validation and CSRF protection.
- Keep the existing search control and make it filter persisted plans server-side.
- Seed one sample plan idempotently:
  - Name: `ADVOCACY PLAN 649/45MBPS`
  - Profile: `pppoe-45mbit/649`
  - Type: `Monthly Recurring Charge (MRC)`
  - Speed: `45 Mbps`
  - Price: `649.00`
  - Users: `106`

## Non-goals

- Subscriber-to-plan relationships, derived subscriber counts, MikroTik synchronization, billing transactions, and generic inventory products are not part of this change.
- The user count is not editable. Seeded data may provide a count; newly added plans start at zero until subscriber integration is implemented.
- Profile groups are the two preview choices shown by the references: `default-encryption` and `pppoe-45mbit/649`.

## Constraints

- Use the project's Laravel 13, Blade, Alpine, Tailwind, Pest, and native `<dialog>` patterns without adding dependencies.
- Keep panel access middleware and implicit route-model binding.
- Store transaction types as stable codes and display these labels: MRC, OTC, PCF, MRD, and MDS.
- Store speed as a positive whole number from `1` to `100000` Mbps. The incremental column remains nullable so legacy plans continue to render as `Speed not set` until edited.
- Store money as `decimal(10,2)` and render Philippine peso values with two decimal places.
- Validation failures must return entered data, inline errors, and reopen the relevant dialog.

## Approaches

1. Static Blade cards: smallest change, but Add would be misleading and data would not persist.
2. Alpine session-only cards: interactive without a database, but all changes disappear on reload.
3. Database-backed plans: slightly larger, but provides reliable CRUD and matches the user's direction to use the database when needed.

The selected approach is database-backed plans, scoped only to this catalogue.

## Interaction Design

The page presents search and Add controls above a responsive card grid. Each semantic plan card shows name, profile group, full transaction-type label, speed above the formatted price, and user count, plus accessible Edit and Delete actions. Add and Edit use labelled native dialogs with Speed immediately above Price; Cancel and Escape close without changes, while Delete requires confirmation. Empty catalogue and empty search results use explicit non-table empty states.
