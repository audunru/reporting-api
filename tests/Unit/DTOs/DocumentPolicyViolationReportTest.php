<?php

namespace audunru\ReportingApi\Tests\Unit\DTOs;

use audunru\ReportingApi\DTOs\DocumentPolicyViolationReport;
use PHPUnit\Framework\TestCase;

class DocumentPolicyViolationReportTest extends TestCase
{
    public function test_from_array_maps_all_properties(): void
    {
        $data = [
            'type' => 'document-policy-violation',
            'url' => 'https://example.test/page',
            'age' => 0,
            'user_agent' => 'Mozilla/5.0',
            'body' => [
                'featureId' => 'oversized-images',
                'disposition' => 'enforce',
                'sourceFile' => 'https://example.test/page',
                'lineNumber' => 1,
                'columnNumber' => 0,
                'message' => 'img too large',
            ],
        ];

        $report = DocumentPolicyViolationReport::fromArray($data);

        $this->assertSame('document-policy-violation', $report->type);
        $this->assertSame('https://example.test/page', $report->url);
        $this->assertSame(0, $report->age);
        $this->assertSame('Mozilla/5.0', $report->userAgent);
        $this->assertSame('oversized-images', $report->body->featureId);
        $this->assertSame('enforce', $report->body->disposition);
        $this->assertSame('https://example.test/page', $report->body->sourceFile);
        $this->assertSame(1, $report->body->lineNumber);
        $this->assertSame(0, $report->body->columnNumber);
        $this->assertSame('img too large', $report->body->message);
    }

    public function test_from_array_defaults_missing_keys_to_null(): void
    {
        $report = DocumentPolicyViolationReport::fromArray([]);

        $this->assertNull($report->url);
        $this->assertNull($report->body->featureId);
        $this->assertNull($report->body->disposition);
        $this->assertNull($report->body->sourceFile);
        $this->assertNull($report->body->lineNumber);
        $this->assertNull($report->body->columnNumber);
        $this->assertNull($report->body->message);
    }

    public function test_from_array_ignores_values_with_wrong_types(): void
    {
        $report = DocumentPolicyViolationReport::fromArray([
            'type' => 'document-policy-violation',
            'url' => ['x'],
            'age' => 'abc',
            'user_agent' => 1,
            'body' => [
                'featureId' => ['x'],
                'disposition' => ['x'],
                'sourceFile' => ['x'],
                'lineNumber' => ['x'],
                'columnNumber' => ['x'],
                'message' => ['x'],
            ],
        ]);

        $this->assertNull($report->url);
        $this->assertNull($report->age);
        $this->assertNull($report->userAgent);
        foreach (get_object_vars($report->body) as $value) {
            $this->assertNull($value);
        }
    }

    public function test_from_array_ignores_body_with_wrong_type(): void
    {
        $report = DocumentPolicyViolationReport::fromArray(['type' => 'document-policy-violation', 'body' => 'x']);

        foreach (get_object_vars($report->body) as $value) {
            $this->assertNull($value);
        }
    }

    public function test_from_array_falls_back_to_policy_id_when_feature_id_has_wrong_type(): void
    {
        $report = DocumentPolicyViolationReport::fromArray([
            'type' => 'document-policy-violation',
            'body' => ['featureId' => ['x'], 'policyId' => 'sync-xhr'],
        ]);

        $this->assertSame('sync-xhr', $report->body->featureId);
    }
}
