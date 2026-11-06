# reporting-api

Laravel package that receives W3C Reporting API and CSP violation reports and dispatches a Laravel event for each report type. It accepts both the legacy `application/csp-report` format and the modern `application/reports+json` format.

## Namespace

`audunru\ReportingApi\`

## Environment variables

| Variable | Default | Description |
|---|---|---|
| `REPORTING_API_PATH` | `/reports` | Endpoint URL path |
| `REPORTING_API_THROTTLE` | `60,1` | Throttle (named limiter or `attempts,minutes`) |
| `REPORTING_API_MAX_BODY_SIZE` | `65536` | Maximum request body size in bytes |
| `REPORTING_API_MAX_REPORTS` | `50` | Maximum number of reports in one `application/reports+json` request |

## How it works

1. `ReportingApiServiceProvider` extends `Spatie\LaravelPackageTools\PackageServiceProvider`
2. `configurePackage()` registers `config/reporting-api.php` (publishable) and `routes/reporting-api.php`; `packageBooted()` aliases the `reporting-endpoints` middleware
3. The route is CSRF-exempt (`VerifyCsrfToken`, `ValidateCsrfToken` and `PreventRequestForgery` are removed) and throttled — browsers post reports unauthenticated
4. `ReportingApiController::report()` checks `Content-Type`:
   - `application/reports+json` → decode JSON array, dispatch typed event per item (matched on `type`)
   - `application/csp-report` → decode JSON object, rename the kebab-case keys of `csp-report` to the Reporting API names (e.g. `blocked-uri` → `blockedURL`, `violated-directive` → `effectiveDirective` as fallback), dispatch `CspViolationReceived`
   - anything else → 400 Bad Request
   - malformed JSON → 400; items without `type` within a valid payload → silently skipped
5. All responses are `204 No Content` on success, `400 Bad Request` for unsupported Content-Type or malformed JSON, `413 Payload Too Large` for a body larger than `max_body_size` (checked before decoding) or for more than `max_reports` reports (checked before dispatching)
6. Each event keeps the raw array; `getReport()` builds the typed DTO from it with `fromArray()` on each call
7. Listeners are **not** registered by the package. Consumers register `LogCspViolation`, `LogReport` or their own listeners in their application. `LogCspViolation` logs `CspViolationReceived`; `LogReport` logs every `ReportEvent` except `csp-violation`

## Testing

Tests use Orchestra Testbench (not a full Laravel app). The service provider is loaded in `TestCase::getPackageProviders()`, which boots the route automatically. HTTP requests use `$this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => '...'], $rawBody)` to send raw JSON bodies with explicit Content-Type headers. `tests/Feature/` has HTTP round-trips per report type, Content-Type routing, request limits and the middleware; `tests/Unit/` has `DTOs/` and `Listeners/`.

## Conventions / Gotchas

- All report data is untrusted. DTOs read values through `ReadsTypedValues`, so a value with the wrong JSON type becomes `null` instead of an error.
- Log messages pass values through `sanitizeForLog()` / `sanitizeUrlForLog()` (control characters removed; query string and fragment removed from URLs). The raw report in the log context is not sanitized; subclasses override `reportForLog()` to change it.
- Listener subclasses customize with `protected string $channel` (default `stack`) and `shouldExclude()`.
- The README documents the public API (events, DTOs, listeners, middleware). Update it when the API changes.

## Origin

The original package was `audunru/csp-report` (CSP-only). It was renamed to `audunru/reporting-api` and expanded to handle all W3C Reporting API report types with an event-driven architecture.
