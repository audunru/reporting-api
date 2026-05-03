# Receive [W3C Reporting API](https://www.w3.org/TR/reporting/) and [CSP violation](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP) reports in Laravel

[![Build Status](https://github.com/audunru/reporting-api/actions/workflows/validate.yml/badge.svg)](https://github.com/audunru/reporting-api/actions/workflows/validate.yml)
[![Coverage Status](https://coveralls.io/repos/github/audunru/reporting-api/badge.svg?branch=main)](https://coveralls.io/github/audunru/reporting-api?branch=main)

Browsers send batched reports — CSP violations, deprecations, network errors, crashes, and more — to a configured endpoint. This package registers that endpoint, decodes the payload, and dispatches Laravel events for each report type.

## Requirements

- PHP 8.3+
- Laravel 13+

## Installation

```bash
composer require audunru/reporting-api
```

The service provider is auto-discovered. The package registers a `POST /reports` route automatically.

## Sending reports from a browser

### Legacy CSP reports (`application/csp-report`)

Set the `report-uri` directive in your `Content-Security-Policy` header:

```
Content-Security-Policy: default-src 'self'; report-uri /reports
```

With [spatie/laravel-csp](https://github.com/spatie/laravel-csp):

```php
// config/csp.php
'report_uri' => env('CSP_REPORT_URI', '/reports'),
```

### Modern Reporting API (`application/reports+json`)

Use `Reporting-Endpoints` and `report-to` to send batched reports in the modern format:

```
Reporting-Endpoints: default="/reports"
Content-Security-Policy: default-src 'self'; report-to default
```

The modern format supports additional report types beyond CSP violations (deprecations, network errors, crashes, etc.).

## Getting started

When a report arrives the package dispatches a Laravel event based on the report type. The package ships two ready-made listeners — `LogCspViolation` and `LogReport` — that you can register directly in `AppServiceProvider::boot()`:

```php
use audunru\ReportingApi\Contracts\ReportEvent;
use audunru\ReportingApi\Events\CspViolationReceived;
use audunru\ReportingApi\Listeners\LogCspViolation;
use audunru\ReportingApi\Listeners\LogReport;
use Illuminate\Support\Facades\Event;

public function boot(): void
{
    Event::listen(CspViolationReceived::class, LogCspViolation::class);
    Event::listen(ReportEvent::class, LogReport::class);
}
```

`LogCspViolation` logs CSP violations as `warning`. `LogReport` logs every other report type as `info`, with the full raw report in the log context. Neither is registered automatically.

Both log to the `stack` channel by default. Override `protected string $channel` to redirect to a different channel:

```php
class MyCspViolationListener extends LogCspViolation
{
    protected string $channel = 'security';
}
```

### Filtering noise with `shouldExclude()`

Browser extensions routinely trigger CSP reports. Override `shouldExclude()` in a subclass to filter them out:

```php
// app/Listeners/MyCspViolationListener.php
namespace App\Listeners;

use audunru\ReportingApi\DTOs\CspViolationReport;
use audunru\ReportingApi\Listeners\LogCspViolation;

class MyCspViolationListener extends LogCspViolation
{
    private const EXTENSION_SCHEMES = [
        'chrome-extension://',
        'moz-extension://',
        'safari-extension://',
    ];

    protected function shouldExclude(CspViolationReport $report): bool
    {
        $blocked = $report->body->blockedURL ?? '';

        foreach (self::EXTENSION_SCHEMES as $scheme) {
            if (str_starts_with($blocked, $scheme)) {
                return true;
            }
        }

        return false;
    }
}
```

`LogReport` supports the same pattern via its `Report` base type:

```php
// app/Listeners/MyReportListener.php
namespace App\Listeners;

use audunru\ReportingApi\DTOs\Report;
use audunru\ReportingApi\Listeners\LogReport;

class MyReportListener extends LogReport
{
    protected function shouldExclude(Report $report): bool
    {
        return $report->type === 'csp-violation'; // handled separately
    }
}
```

Register your subclasses the same way:

```php
use audunru\ReportingApi\Contracts\ReportEvent;
use audunru\ReportingApi\Events\CspViolationReceived;
use App\Listeners\MyCspViolationListener;
use App\Listeners\MyReportListener;
use Illuminate\Support\Facades\Event;

public function boot(): void
{
    Event::listen(CspViolationReceived::class, MyCspViolationListener::class);
    Event::listen(ReportEvent::class, MyReportListener::class);
}
```

## Middleware

The package registers a `reporting-endpoints` middleware alias that adds the `Reporting-Endpoints` header to responses. Browsers use this header to discover where to POST their reports.

Apply it to specific routes or route groups:

```php
Route::middleware('reporting-endpoints')->group(function () {
    Route::get('/', HomeController::class);
});
```

To add it globally to all web routes (Laravel 11+, `bootstrap/app.php`):

```php
use audunru\ReportingApi\Http\Middleware\AddReportingEndpointsHeader;
use Illuminate\Foundation\Configuration\Middleware;

->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        AddReportingEndpointsHeader::class,
    ]);
})
```

The header value uses the `path` from your config:

```
Reporting-Endpoints: default="/reports"
```

## Configuration

Publish the config file to customise the endpoint path and throttle limit:

```bash
php artisan vendor:publish --tag=reporting-api-config
```

| Key | Env var | Default | Description |
|-----|---------|---------|-------------|
| `path` | `REPORTING_API_PATH` | `/reports` | URL path of the report endpoint |
| `throttle` | `REPORTING_API_THROTTLE` | `60,1` | Throttle value — named limiter or `attempts,minutes` |

## Reference

### Dispatched events

| Event class | Trigger |
|---|---|
| `CspViolationReceived` | `csp-violation` type (modern) or `application/csp-report` (legacy) |
| `DeprecationReportReceived` | `deprecation` type |
| `InterventionReportReceived` | `intervention` type |
| `CrashReportReceived` | `crash` type |
| `NetworkErrorReceived` | `network-error` type |
| `CoepReportReceived` | `coep` type |
| `CoopReportReceived` | `coop` type |
| `DocumentPolicyViolationReceived` | `document-policy-violation` type |
| `GenericReportReceived` | Any unrecognized type |

### Event interface

All event classes implement `audunru\ReportingApi\Contracts\ReportEvent` and expose:

| Method | Returns |
|--------|---------|
| `getReport()` | Typed report DTO (e.g. `CspViolationReport`) |
| `getRawReport()` | Raw report array as received from the browser |

### Report DTOs

`getReport()` returns a typed DTO that extends `audunru\ReportingApi\DTOs\Report`, with properties common to all report types:

| Property | Type | Description |
|----------|------|-------------|
| `type` | `string` | W3C report type (e.g. `'csp-violation'`) |
| `url` | `?string` | URL of the page that generated the report |
| `age` | `?int` | Milliseconds between report generation and sending |
| `userAgent` | `?string` | Browser user agent string |

Each specific report DTO also has a typed `body` property whose class matches the report type:

| Event | `getReport()` returns | `body` type |
|---|---|---|
| `CspViolationReceived` | `CspViolationReport` | `CspViolationReportBody` |
| `DeprecationReportReceived` | `DeprecationReport` | `DeprecationReportBody` |
| `InterventionReportReceived` | `InterventionReport` | `InterventionReportBody` |
| `CrashReportReceived` | `CrashReport` | `CrashReportBody` |
| `NetworkErrorReceived` | `NetworkErrorReport` | `NetworkErrorReportBody` |
| `CoepReportReceived` | `CoepViolationReport` | `CoepViolationReportBody` |
| `CoopReportReceived` | `CoopViolationReport` | `CoopViolationReportBody` |
| `DocumentPolicyViolationReceived` | `DocumentPolicyViolationReport` | `DocumentPolicyViolationReportBody` |
| `GenericReportReceived` | `GenericReport` | `?array` |

Body classes are plain PHP objects with nullable readonly properties matching the W3C specification for that report type. For example, `CspViolationReportBody` exposes `blockedURL`, `effectiveDirective`, `disposition`, `documentURL`, `originalPolicy`, and so on.
