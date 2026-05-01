# reporting-api

Receive and handle [W3C Reporting API](https://www.w3.org/TR/reporting/) and [Content Security Policy](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP) violation reports in Laravel.

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

## Listening for events

When a report arrives the package dispatches a specific Laravel event based on the report type. Register your own listeners in your application's `EventServiceProvider`:

```php
use audunru\ReportingApi\Events\CspViolationReceived;
use audunru\ReportingApi\Events\DeprecationReportReceived;

protected $listen = [
    CspViolationReceived::class => [
        YourCspViolationListener::class,
    ],
    DeprecationReportReceived::class => [
        YourDeprecationListener::class,
    ],
];
```

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

### Logging CSP violations

To log CSP violations, create a listener in your application. Browser extensions inject scripts and styles that routinely trigger CSP reports — filter them out before logging to avoid noise:

```php
// app/Listeners/LogCspViolation.php
namespace App\Listeners;

use audunru\ReportingApi\DTOs\Bodies\CspViolationReportBody;
use audunru\ReportingApi\Events\CspViolationReceived;
use Illuminate\Support\Facades\Log;

class LogCspViolation
{
    private const EXTENSION_SCHEMES = [
        'chrome-extension://',
        'moz-extension://',
        'safari-extension://',
    ];

    public function handle(CspViolationReceived $event): void
    {
        $body = $event->getReport()->body;

        if ($this->isExtensionNoise($body)) {
            return;
        }

        Log::warning('CSP violation: {directive} blocked {url}', [
            'directive' => $body->effectiveDirective,
            'url'       => $body->blockedURL,
            'page'      => $event->getReport()->url,
        ]);
    }

    private function isExtensionNoise(CspViolationReportBody $body): bool
    {
        $blocked = $body->blockedURL ?? '';

        foreach (self::EXTENSION_SCHEMES as $scheme) {
            if (str_starts_with($blocked, $scheme)) {
                return true;
            }
        }

        return false;
    }
}
```

Register it in your `EventServiceProvider`:

```php
use audunru\ReportingApi\Events\CspViolationReceived;
use App\Listeners\LogCspViolation;

protected $listen = [
    CspViolationReceived::class => [LogCspViolation::class],
];
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
