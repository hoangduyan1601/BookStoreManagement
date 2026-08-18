# Portfolio Audit

## Before

Initial CV readiness: **58/100**. The application had meaningful features and working builds, but a committed VNPay secret, unauthenticated payment webhook, plain-text password fallback, missing ownership checks, unreliable test isolation, mojibake README text, and deployment scripts that ignored migration failures made it unsuitable as a primary portfolio project.

## Changes Made

- Moved payment credentials to environment-backed configuration.
- Added constant-time VNPay signature comparisons and order ownership checks.
- Required a token for the bank webhook and stopped logging full webhook payloads.
- Removed plain-text authentication and regenerated sessions after login.
- Added backend validation for registration and checkout.
- Protected order status and notification mutations by customer ownership.
- Disabled client-side confirmation of bank payments.
- Added focused security regression tests and in-memory SQLite test configuration.
- Made Docker migrations fail visibly and made seed data opt-in.
- Replaced the broken README with reproducible setup, architecture, security, test, and deployment instructions.

## Why

These changes address risks a reviewer can verify directly: credential handling, authorization boundaries, reliable tests, safe configuration, and a clone-to-run workflow. The implementation deliberately keeps the existing Laravel monolith and Vietnamese domain schema.

## Remaining Issues

- The checkout and payment controllers are still large and should be extracted incrementally.
- Stock updates need row locks and explicit availability checks for concurrency safety.
- Generic webhook processing needs provider transaction IDs for replay protection.
- Several migrations use conditional table creation, which can hide schema drift.
- Historical/generated images and coursework artifacts make the repository larger than necessary; review them manually before removal.
- Existing source text contains encoding damage in some Vietnamese comments/messages.

## CV Readiness

Final assessment after the hardening pass: **91/100 — strong fresher portfolio project**, subject to rotating the exposed VNPay credential, deciding whether to rewrite the public Git history, and adding final GitHub profile links/screenshots.
