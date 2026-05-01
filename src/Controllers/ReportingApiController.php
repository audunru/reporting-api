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

        return response('', 400);
    }

    private function handleModernReports(Request $request): Response
    {
        $reports = json_decode($request->getContent() ?: '[]', true);

        if (is_array($reports)) {
            foreach ($reports as $report) {
                if (! is_array($report) || ! isset($report['type'])) {
                    continue;
                }

                event($this->makeEvent($report));
            }
        }

        return response('', 204);
    }

    private function handleLegacyCspReport(Request $request): Response
    {
        $payload = json_decode($request->getContent() ?: '{}', true);

        if (is_array($payload) && isset($payload['csp-report'])) {
            $normalized = [
                'type' => 'csp-violation',
                'body' => $payload['csp-report'],
            ];
            event(new CspViolationReceived($normalized));
        }

        return response('', 204);
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
