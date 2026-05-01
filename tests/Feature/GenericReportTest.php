<?php

namespace audunru\ReportingApi\Tests\Feature;

use audunru\ReportingApi\Events\GenericReportReceived;
use audunru\ReportingApi\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class GenericReportTest extends TestCase
{
    public function test_unknown_type_dispatches_generic_event(): void
    {
        Event::fake();

        $report = [
            'type' => 'custom-browser-type',
            'age' => 0,
            'url' => 'https://example.test/page',
            'user_agent' => 'Mozilla/5.0',
            'body' => ['detail' => 'some data'],
        ];

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], json_encode([$report]))
            ->assertNoContent();

        Event::assertDispatched(GenericReportReceived::class, function (GenericReportReceived $event) use ($report) {
            return $event->getRawReport() === $report && $event->getReport()->type === 'custom-browser-type';
        });
    }

    public function test_skips_items_without_type(): void
    {
        Event::fake([GenericReportReceived::class]);

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], json_encode([['body' => ['data' => 'value']]]))
            ->assertNoContent();

        Event::assertNotDispatched(GenericReportReceived::class);
    }

    public function test_skips_non_array_items(): void
    {
        Event::fake([GenericReportReceived::class]);

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], json_encode(['not-an-array', null, 42]))
            ->assertNoContent();

        Event::assertNotDispatched(GenericReportReceived::class);
    }
}
