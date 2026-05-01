<?php

namespace audunru\ReportingApi\Tests\Feature;

use audunru\ReportingApi\Events\InterventionReportReceived;
use audunru\ReportingApi\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class InterventionReportTest extends TestCase
{
    public function test_dispatches_event(): void
    {
        Event::fake();

        $report = [
            'type' => 'intervention',
            'age' => 0,
            'url' => 'https://example.test/page',
            'user_agent' => 'Mozilla/5.0',
            'body' => ['id' => 'HeavyAdIntervention', 'message' => 'intervention applied'],
        ];

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], json_encode([$report]))
            ->assertNoContent();

        Event::assertDispatched(InterventionReportReceived::class, function (InterventionReportReceived $event) use ($report) {
            return $event->getRawReport() === $report;
        });
    }
}
