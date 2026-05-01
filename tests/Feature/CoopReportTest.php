<?php

namespace audunru\ReportingApi\Tests\Feature;

use audunru\ReportingApi\Events\CoopReportReceived;
use audunru\ReportingApi\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class CoopReportTest extends TestCase
{
    public function test_dispatches_event(): void
    {
        Event::fake();

        $report = [
            'type' => 'coop',
            'age' => 0,
            'url' => 'https://example.test/page',
            'user_agent' => 'Mozilla/5.0',
            'body' => ['blockedURL' => 'https://popup.example.com', 'disposition' => 'enforce', 'effectivePolicy' => 'same-origin', 'type' => 'navigate-to-document'],
        ];

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], json_encode([$report]))
            ->assertNoContent();

        Event::assertDispatched(CoopReportReceived::class, function (CoopReportReceived $event) use ($report) {
            return $event->getRawReport() === $report;
        });
    }
}
