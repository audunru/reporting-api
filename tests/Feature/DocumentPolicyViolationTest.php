<?php

namespace audunru\ReportingApi\Tests\Feature;

use audunru\ReportingApi\Events\DocumentPolicyViolationReceived;
use audunru\ReportingApi\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class DocumentPolicyViolationTest extends TestCase
{
    public function test_dispatches_event(): void
    {
        Event::fake();

        $report = [
            'type' => 'document-policy-violation',
            'age' => 0,
            'url' => 'https://example.test/page',
            'user_agent' => 'Mozilla/5.0',
            'body' => ['featureId' => 'oversized-images', 'disposition' => 'enforce', 'message' => 'img too large'],
        ];

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], json_encode([$report]))
            ->assertNoContent();

        Event::assertDispatched(DocumentPolicyViolationReceived::class, function (DocumentPolicyViolationReceived $event) use ($report) {
            return $event->getRawReport() === $report;
        });
    }
}
