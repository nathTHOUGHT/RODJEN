# Subscriber Persistence Design

## Intent

Turn the existing Add Subscriber, Subscriber Records, and Subscriber Details screens into one working database-backed flow while preserving the current visual design.

## Scope

Included: create a subscriber, validate input, store personal/service/network data, list saved subscribers, filter records, and display one saved subscriber. Excluded: editing, deletion, billing transactions, comments, MikroTik API provisioning, and other currently disabled actions.

## Architecture

Use three related tables so customer identity is not mixed with service or network credentials:

- `subscribers`: account identity, personal/contact information, location, and remarks.
- `subscriptions`: the subscriber's current service, optional plan, dates, billing day, status, and credit limit.
- `network_accounts`: PPPoE credentials and access-network metadata for the subscription.

The initial UI creates one subscription and one network account per subscriber. The schema keeps the boundaries explicit so additional services can be supported later without redesigning personal data.

## Data and security rules

- Account numbers and PPPoE usernames are strings; leading zeroes must be preserved.
- Account numbers and non-null PPPoE usernames are unique in both validation and the database.
- PPPoE passwords are encrypted at rest, excluded from serialization, never flashed as old input, and never rendered back to the browser.
- Only monthly recurring charge (`mrc`) plans are offered and accepted; a subscriber may initially have no plan.
- Deleting a plan sets the subscription's `plan_id` to null rather than deleting subscriber records.
- Creation of all related rows and plan usage-count adjustment occurs in one database transaction.

## Interface

Keep the existing route names `subscribers.create`, `subscribers.index`, and `subscribers.details`; add `subscribers.store`. The details route receives a numeric subscriber ID. Records support optional area, status, and free-text filters. Empty and unavailable data are shown honestly rather than using demo values.

## Approaches considered

A single wide `subscribers` table is faster initially but couples personal, service, and network data. A fully decomposed address/contact/topology schema is more flexible but excessive for the first usable workflow. The selected three-table design is the smallest normalized foundation that matches the existing form and future ISP requirements.
