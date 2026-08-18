# Interview Notes

## Project Overview

This is a Laravel monolith for a small bookstore. Customers browse books, manage a cart, apply promotions, place orders, pay through VNPay or bank transfer, and track orders. Staff use a protected admin area for catalog, inventory, customers, orders, notifications, articles, and revenue views.

## Architecture

Routes apply authentication and role middleware before controllers coordinate workflows. Eloquent models map Vietnamese domain tables and relationships. Blade renders both storefront and admin screens. `GeminiService` owns the external AI call; payment providers call dedicated endpoints.

## Why This Tech Stack?

Laravel provides routing, validation, CSRF protection, authentication primitives, migrations, ORM, notifications, and testing in one approachable framework. A monolith is appropriate for this project size: it is easier to run and explain than unnecessary microservices. SQLite makes evaluation quick, while MySQL remains available for deployment.

## Database Design

Products belong to categories and publishers and have a many-to-many author relation. Carts/orders use header and detail tables with composite keys. Customers link to accounts, and orders snapshot unit price so later product price changes do not alter history. Foreign keys and cascades protect core relations.

## Most Difficult Problem

Checkout changes an order, detail rows, inventory counts, sold counts, and cart rows together. These changes belong in a transaction so partial orders are not stored. A remaining improvement is row locking and explicit stock validation to handle concurrent purchases.

## Authentication Flow

Registration validates input, hashes the password, and creates account/customer records in one transaction. Login checks Laravel-compatible password hashes, logs in the active account, and regenerates the session ID. Admin routes additionally check normalized roles in middleware. Logout invalidates the session and renews the CSRF token.

## Important Business Logic

- Checkout recalculates totals from database product prices, not browser-submitted prices.
- Promotions are rechecked before order creation.
- Payment callbacks compare order amount and accepted order states.
- Customer resources are filtered by the authenticated customer's ID.

## Security Decisions

- Secrets live in environment variables and `.env` is ignored.
- VNPay responses use HMAC verification with constant-time comparison.
- The generic bank webhook requires a configured bearer/header token.
- Customers cannot mark their own bank transfer as paid; only a verified webhook can.
- Validation and CSRF protection are enforced server-side.

The VNPay key found in the original Git history must still be rotated; deleting it from the current file does not erase history.

## Trade-offs

The existing Vietnamese table/column names were retained to avoid a risky rewrite. Some business logic remains in large controllers because a complete service-layer migration would create unnecessary regression risk. The next extraction target should be payment/order state transitions.

## Bugs/Issues Solved

- Removed committed VNPay credentials and sensitive payment URL logging.
- Removed plain-text password fallback and added session regeneration.
- Protected webhook and customer-owned order/notification endpoints.
- Prevented client-side bank-transfer confirmation.
- Isolated automated tests from the development database.
- Made container migration failures visible and seeding opt-in.

## Future Improvements

- Add webhook replay protection using provider transaction IDs.
- Add stock row locks and insufficient-stock validation.
- Add policies/Form Requests as the number of endpoints grows.
- Add GitHub Actions and deployment-specific health checks.

## Common Interview Questions

1. Why did you choose a monolith instead of microservices?
2. Which checkout operations must be in one transaction?
3. How do you prevent a user from viewing another user's order?
4. Why is a webhook token or signature necessary?
5. Why use `hash_equals` for payment signatures?
6. What happens if two users buy the final copy simultaneously?
7. Why store the order line price separately from product price?
8. How does Laravel protect forms against CSRF?
9. What would you extract into a service next, and why?
10. How would you make payment processing idempotent?

## Checkout Transaction

The checkout transaction begins before stock is validated. Product rows are loaded in a stable ID order with `lockForUpdate()`, then every requested quantity is checked before the order header or any detail row is created. The same locked product instances are used to calculate line prices and decrement inventory. If one item is unavailable, a validation exception rolls back the order, details, stock counters, and cart changes together.

The lock matters when two requests try to purchase the final copy. One transaction obtains the row lock first; the second waits, then reads the committed lower stock and fails validation instead of driving inventory below zero. SQLite serializes writes differently, while MySQL uses the explicit row locks in deployment.

## Payment Idempotency

Each bank callback must include a provider transaction reference. Successful callbacks are recorded in `payment_transactions`, protected by a unique `(provider, transaction_id)` constraint. Processing also locks the target order before checking its state. A duplicate callback returns an `already_processed` result and does not create a second notification or repeat the state transition.

The transaction reference protects against replay of the same provider event; the order state protects against a different reference attempting to pay an already finalized order. Email is sent after the database transaction so a slow mail transport does not hold database locks.

## Security Bugs Fixed

During a security review I identified hard-coded VNPay credentials, an unauthenticated payment webhook, a plain-text password fallback, missing customer ownership checks, and a client-controlled bank confirmation endpoint. I moved credentials to environment configuration, added constant-time signature checks and webhook authentication, removed the password fallback, regenerated sessions after login, scoped customer queries, and limited payment transitions to verified callbacks.

I also reviewed reliability rather than only obvious security bugs. Checkout originally read stock before its transaction, so concurrent purchases could oversell. I moved stock validation under row locks and added rollback tests. The webhook originally relied only on order status; I added persisted transaction references and a unique database constraint so duplicate callbacks are explicitly idempotent.
