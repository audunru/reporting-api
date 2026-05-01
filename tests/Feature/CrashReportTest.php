<?php

namespace audunru\ReportingApi\Tests\Feature;

use audunru\ReportingApi\Events\CrashReportReceived;
use audunru\ReportingApi\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class CrashReportTest extends TestCase
{
    public function test_dispatches_event(): void
    {
        Event::fake();

        $report = [
            'type' => 'crash',
            'age' => 0,
            'url' => 'https://example.test/page',
            'user_agent' => 'Mozilla/5.0',
            'body' => ['reason' => 'oom'],
        ];

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], json_encode([$report]))
            ->assertNoContent();

        Event::assertDispatched(CrashReportReceived::class, function (CrashReportReceived $event) use ($report) {
            return $event->getRawReport() === $report;
        });
    }
}
