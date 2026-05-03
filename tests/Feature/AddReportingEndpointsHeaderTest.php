<?php

namespace audunru\ReportingApi\Tests\Feature;

use audunru\ReportingApi\Tests\TestCase;

class AddReportingEndpointsHeaderTest extends TestCase
{
    protected function defineRoutes($router): void
    {
        $router->get('/header-test', fn () => response('ok'))
            ->middleware('reporting-endpoints');
    }

    public function test_adds_reporting_endpoints_header_with_default_path(): void
    {
        $this->get('/header-test')
            ->assertHeader('Reporting-Endpoints', 'default="/reports"');
    }

    public function test_adds_reporting_endpoints_header_with_custom_path(): void
    {
        config(['reporting-api.path' => '/csp']);

        $this->get('/header-test')
            ->assertHeader('Reporting-Endpoints', 'default="/csp"');
    }
}
