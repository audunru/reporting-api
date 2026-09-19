# AGENTS.md — reporting-api

Laravel package that receives and handles W3C Reporting API and CSP violation reports. Dispatches Laravel events for each report type. Extracted from the [Skrapfanten](https://github.com/audunru/skrapfanten) application and expanded to support both the legacy `application/csp-report` format and the modern `application/reports+json` format.

## Package structure

```
reporting-api/
├── src/
│   ├── ReportingApiServiceProvider.php     — registers route and config
│   ├── Contracts/
│   │   └── ReportEvent.php                 — interface: getType(), getBody()
│   ├── Controllers/
│   │   └── ReportingApiController.php      — Content-Type routing → event dispatch
│   └── Events/
│       ├── CspViolationReceived.php
│       ├── DeprecationReportReceived.php
│       ├── InterventionReportReceived.php
│       ├── CrashReportReceived.php
│       ├── NetworkErrorReceived.php
│       ├── CoepReportReceived.php
│       ├── CoopReportReceived.php
│       ├── DocumentPolicyViolationReceived.php
│       └── GenericReportReceived.php       — fallback for unrecognized types
├── config/
│   └── reporting-api.php                   — path, throttle
├── routes/
│   └── reporting-api.php                   — POST /reports (CSRF-exempt, throttled)
└── tests/
    ├── TestCase.php                         — Orchestra Testbench base
    └── Feature/
        ├── ContentTypeRoutingTest.php
        ├── CspViolationTest.php
        ├── DeprecationReportTest.php
        ├── InterventionReportTest.php
        ├── CrashReportTest.php
        ├── NetworkErrorReportTest.php
        ├── CoepReportTest.php
        ├── CoopReportTest.php
        ├── DocumentPolicyViolationTest.php
        └── GenericReportTest.php
```

## Namespace

`audunru\ReportingApi\`

## Commands

```bash
composer test              # Run PHPUnit tests via Orchestra Testbench
composer fix               # Auto-fix code style with Laravel Pint
composer verify            # Pint (dry-run) + phpmd + phpunit
composer test-with-coverage  # Tests with Clover XML coverage output
```

## Environment variables

| Variable | Default | Description |
|---|---|---|
| `REPORTING_API_PATH` | `/reports` | Endpoint URL path |
| `REPORTING_API_THROTTLE` | `60,1` | Throttle (named limiter or `attempts,minutes`) |

## How it works

1. `ReportingApiServiceProvider` extends `Spatie\LaravelPackageTools\PackageServiceProvider`
2. `configurePackage()` registers `config/reporting-api.php` (publishable) and `routes/reporting-api.php`
3. The route is CSRF-exempt and throttled — browsers post reports unauthenticated
4. `ReportingApiController::report()` checks `Content-Type`:
   - `application/reports+json` → decode JSON array, dispatch typed event per item
   - `application/csp-report` → decode JSON object, normalize to `{type, body}`, dispatch `CspViolationReceived`
   - anything else → 400 Bad Request
   - malformed items within a valid payload → silently skipped
5. All responses are `204 No Content` on success, `400 Bad Request` for unsupported Content-Type
6. Consumers register their own listeners in their application's `EventServiceProvider`

## Testing

Tests use Orchestra Testbench (not a full Laravel app). The service provider is loaded in `TestCase::getPackageProviders()`, which boots the route automatically. HTTP requests use `$this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => '...'], $rawBody)` to send raw JSON bodies with explicit Content-Type headers. Run with `composer test`.

## Code style

- Laravel Pint with default Laravel preset (no custom `pint.json`)
- PHPMD with `phpmd-ruleset.xml` (shared across all packages in the parent directory)

## Origin

The controller and tests were extracted from `audunru/skrapfanten`:
- `backend/app/Logging/Controllers/CspReportController.php`
- `backend/tests/Feature/CspReportControllerTest.php`

The original package was `audunru/csp-report` (CSP-only). It was renamed to `audunru/reporting-api` and expanded to handle all W3C Reporting API report types with an event-driven architecture.
