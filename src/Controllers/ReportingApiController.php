<?php

namespace audunru\ReportingApi\Controllers;

use audunru\ReportingApi\Events\CoepReportReceived;
use audunru\ReportingApi\Events\CoopReportReceived;
use audunru\ReportingApi\Events\CrashReportReceived;
use audunru\ReportingApi\Events\CspViolationReceived;
use audunru\ReportingApi\Events\DeprecationReportReceived;
use audunru\ReportingApi\Events\DocumentPolicyViolationReceived;
use audunru\ReportingApi\Events\GenericReportReceived;
use audunru\ReportingApi\Events\InterventionReportReceived;
use audunru\ReportingApi\Events\NetworkErrorReceived;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use JsonException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ReportingApiController extends Controller
{
    private const LEGACY_CSP_KEYS = [
        'blocked-uri' => 'blockedURL',
        'column-number' => 'columnNumber',
        'disposition' => 'disposition',
        'document-uri' => 'documentURL',
        'effective-directive' => 'effectiveDirective',
        'line-number' => 'lineNumber',
        'original-policy' => 'originalPolicy',
        'referrer' => 'referrer',
        'script-sample' => 'sample',
        'source-file' => 'sourceFile',
        'status-code' => 'statusCode',
    ];

    public function report(Request $request): Response
    {
        if (strlen($request->getContent()) > config('reporting-api.max_body_size', 65536)) {
            return response('', Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }

        $contentType = $request->header('Content-Type', '');

        if (str_contains($contentType, 'application/reports+json')) {
            return $this->handleModernReports($request);
        }

        if (str_contains($contentType, 'application/csp-report')) {
            return $this->handleLegacyCspReport($request);
        }

        return response('', Response::HTTP_BAD_REQUEST);
    }

    private function handleModernReports(Request $request): Response
    {
        try {
            $reports = json_decode($request->getContent() ?: '[]', true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return response('', Response::HTTP_BAD_REQUEST);
        }

        if (is_array($reports) && count($reports) > config('reporting-api.max_reports', 50)) {
            return response('', Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }

        if (is_array($reports)) {
            foreach ($reports as $report) {
                if (! is_array($report) || ! isset($report['type'])) {
                    continue;
                }

                event($this->makeEvent($report));
            }
        }

        return response('', Response::HTTP_NO_CONTENT);
    }

    private function handleLegacyCspReport(Request $request): Response
    {
        try {
            $payload = json_decode($request->getContent() ?: '{}', true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return response('', Response::HTTP_BAD_REQUEST);
        }

        if (is_array($payload) && is_array($payload['csp-report'] ?? null)) {
            $body = $this->mapLegacyCspBody($payload['csp-report']);
            $normalized = array_filter([
                'type' => 'csp-violation',
                'url' => $body['documentURL'] ?? null,
                'body' => $body,
            ], fn ($value) => $value !== null);
            event(new CspViolationReceived($normalized));
        }

        return response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Rename the kebab-case keys of a legacy CSP report to the names that the Reporting API uses.
     */
    private function mapLegacyCspBody(array $legacy): array
    {
        $body = [];

        foreach (self::LEGACY_CSP_KEYS as $legacyKey => $key) {
            if (array_key_exists($legacyKey, $legacy)) {
                $body[$key] = $legacy[$legacyKey];
            }
        }

        if (! array_key_exists('effectiveDirective', $body) && array_key_exists('violated-directive', $legacy)) {
            $body['effectiveDirective'] = $legacy['violated-directive'];
        }

        return $body;
    }

    private function makeEvent(array $report): object
    {
        return match ($report['type']) {
            'csp-violation' => new CspViolationReceived($report),
            'deprecation' => new DeprecationReportReceived($report),
            'intervention' => new InterventionReportReceived($report),
            'crash' => new CrashReportReceived($report),
            'network-error' => new NetworkErrorReceived($report),
            'coep' => new CoepReportReceived($report),
            'coop' => new CoopReportReceived($report),
            'document-policy-violation' => new DocumentPolicyViolationReceived($report),
            default => new GenericReportReceived($report),
        };
    }
}
