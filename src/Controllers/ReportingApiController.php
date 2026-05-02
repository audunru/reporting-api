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
    public function report(Request $request): Response
    {
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

        if (is_array($payload) && isset($payload['csp-report'])) {
            $normalized = [
                'type' => 'csp-violation',
                'body' => $payload['csp-report'],
            ];
            event(new CspViolationReceived($normalized));
        }

        return response('', Response::HTTP_NO_CONTENT);
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
