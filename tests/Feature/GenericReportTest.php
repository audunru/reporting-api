<?php

namespace audunru\ReportingApi\Tests\Feature;

use audunru\ReportingApi\Contracts\ReportEvent;
use audunru\ReportingApi\Events\CspViolationReceived;
use audunru\ReportingApi\Events\GenericReportReceived;
use audunru\ReportingApi\Listeners\LogCspViolation;
use audunru\ReportingApi\Listeners\LogReport;
use audunru\ReportingApi\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\DataProvider;

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

    public static function reportsWithWrongFieldTypes(): array
    {
        return [
            'url is an array' => [['type' => 'deprecation', 'url' => ['x']]],
            'age is a string' => [['type' => 'deprecation', 'age' => 'abc']],
            'body is a string' => [['type' => 'deprecation', 'body' => 'x']],
            'type is an array' => [['type' => ['x']]],
            'csp body is a string' => [['type' => 'csp-violation', 'body' => 'x']],
            'csp body field is an array' => [['type' => 'csp-violation', 'body' => ['blockedURL' => ['x']]]],
        ];
    }

    #[DataProvider('reportsWithWrongFieldTypes')]
    public function test_report_with_wrong_field_types_does_not_fail(array $report): void
    {
        $this->withoutExceptionHandling();
        Event::listen(CspViolationReceived::class, LogCspViolation::class);
        Event::listen(ReportEvent::class, LogReport::class);

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], json_encode([$report]))
            ->assertNoContent();
    }
}
