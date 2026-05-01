<?php

namespace audunru\ReportingApi\Tests\Feature;

use audunru\ReportingApi\Events\CoepReportReceived;
use audunru\ReportingApi\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class CoepReportTest extends TestCase
{
    public function test_dispatches_event(): void
    {
        Event::fake();

        $report = [
            'type' => 'coep',
            'age' => 0,
            'url' => 'https://example.test/page',
            'user_agent' => 'Mozilla/5.0',
            'body' => ['blockedURL' => 'https://cdn.example.com/script.js', 'disposition' => 'enforce', 'type' => 'corp'],
        ];

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], json_encode([$report]))
            ->assertNoContent();

        Event::assertDispatched(CoepReportReceived::class, function (CoepReportReceived $event) use ($report) {
            return $event->getRawReport() === $report;
        });
    }
}
