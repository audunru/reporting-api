<?php

namespace audunru\ReportingApi\Tests\Feature;

use audunru\ReportingApi\Events\NetworkErrorReceived;
use audunru\ReportingApi\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class NetworkErrorReportTest extends TestCase
{
    public function test_dispatches_event(): void
    {
        Event::fake();

        $report = [
            'type' => 'network-error',
            'age' => 0,
            'url' => 'https://example.test/page',
            'user_agent' => 'Mozilla/5.0',
            'body' => ['referrer' => 'https://example.test', 'server_ip' => '1.2.3.4', 'protocol' => 'h2', 'status_code' => 0, 'elapsed_time' => 42, 'phase' => 'dns', 'type' => 'dns.name_not_resolved'],
        ];

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], json_encode([$report]))
            ->assertNoContent();

        Event::assertDispatched(NetworkErrorReceived::class, function (NetworkErrorReceived $event) use ($report) {
            return $event->getRawReport() === $report;
        });
    }
}
