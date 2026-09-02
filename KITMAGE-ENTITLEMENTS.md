# Kitmage Training Entitlements

## Purpose and course mode

This distribution adds Tutor's native **Entitlement Only** access mode. Such courses are neither free nor conventionally purchasable. Tutor self-enrollment endpoints are blocked, but the entitlement service uses the supported completed enrollment call `EnrollmentModel::do_enroll(course_id, 0, user_id)` and verifies `is_enrolled()`.

## WooCommerce configuration and provisioning

A product's **Training Entitlements** data tab enables the feature and selects an entitlement course, positive seats per unit, and a redemption window (30 days by default). Variations can override course, seats, and days; empty values inherit the parent. Simple, variable, simple-subscription and variable-subscription products use the same metadata. Purchased quantity multiplies seats.

Paid orders are processed on WooCommerce payment-complete/processing/completed hooks. Subscription renewal payment completion processes the renewal order as a separate batch; cancellation does not revoke paid batches. The unique `(order_id, order_item_id)` database key makes hook retries idempotent. Woo CRUD (`wc_get_order`, product/order/item objects and metadata) is used, and HPOS compatibility is declared. Failed/unpaid orders produce nothing. Full refunds mark funded batches `refunded`; completed redemptions/enrollments remain. Partial refunds are informational in this release.

## Redemption and accounting

Each batch owns one 256-bit URL-safe shared token at `/training/redeem/{TOKEN}`. A keyed deterministic hash is indexed for lookup; the recoverable value is authenticated-encrypted at rest for the purchaser UI. Regeneration replaces both immediately without changing capacity/history. Anonymous users see safe course/expiry data and use WordPress login with a return URL.

Reservation uses a short transaction and conditional update enforcing `used + reserved < total`, then creates a pending redemption. Tutor enrollment happens outside that transaction. A second transaction finalizes reserved to used, or records failure and releases it. The hourly reconciler inspects Tutor enrollment before finalizing or releasing pending rows older than 15 minutes. Identity name/email snapshots are written only on success.

## Customer and administration

WooCommerce My Account adds **Training Enrollments**, authorized strictly by the current customer ID. It shows historical batches, capacity, status, expiration, usable link, and a roster containing names and redemption dates (not email). WooCommerce → Training Entitlements provides paginated batches and detail records. Administrators with `manage_woocommerce` and a valid nonce can revoke/reactivate, regenerate tokens, change expiration, and adjust totals; totals cannot fall below used plus reserved. Audit rows are append-only in normal UI.

## Tables (schema 1.0.0)

- `wp_kte_batches`: primary `id`; unique `token_hash`; unique provisioning source `(order_id, order_item_id)`; indexes for customer, subscription, course, status and expiration. Stores historical product/course/order configuration, capacity, encrypted token, state and timestamps.
- `wp_kte_redemptions`: primary `id`; unique `(batch_id,user_id)`; indexes for stale pending work and course. Stores state, reservation/completion, snapshots and safe technical failure context.
- `wp_kte_audit_log`: primary `id`; indexes for batch, action and created time. Stores before/after JSON, actor and context.

Data is retained on deactivation and uninstall. Activation runs versioned `dbDelta` migration and flushes rewrite rules once.

## Public hooks

- `kitmage_training_entitlements/batch_created` — batch ID, Woo order ID, order-item ID.
- `kitmage_training_entitlements/redemption_completed` — redemption ID, batch ID, user ID.
- `kitmage_training_entitlements/redemption_failed` — redemption ID, batch ID, failure code.
- `kitmage_training_entitlements/batch_revoked` — batch ID.

## Operational limits

WooCommerce is optional for Tutor itself but required to fund batches. Subscriptions integration activates only when its APIs exist. There is one shared URL per batch: no invitation emails, CSV invitations, rollover, purchaser unenrollment, or automatic unenrollment. Deleted products/courses leave history intact; a missing/non-entitlement course fails new redemption safely. Operational code must never log raw tokens.
