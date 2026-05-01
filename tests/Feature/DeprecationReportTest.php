<?php

namespace audunru\ReportingApi\Tests\Feature;

use audunru\ReportingApi\Events\DeprecationReportReceived;
use audunru\ReportingApi\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class DeprecationReportTest extends TestCase
{
    public function test_dispatches_event(): void
    {
        Event::fake();

        $report = [
            'type' => 'deprecation',
            'age' => 5,
            'url' => 'https://example.test/page',
            'user_agent' => 'Mozilla/5.0',
            'body' => ['id' => 'NavigatorUserAgent', 'message' => 'deprecated'],
        ];

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], json_encode([$report]))
            ->assertNoContent();

        Event::assertDispatched(DeprecationReportReceived::class, function (DeprecationReportReceived $event) use ($report) {
            return $event->getRawReport() === $report;
        });
    }
}
