<?php

namespace audunru\ReportingApi;

use audunru\ReportingApi\Http\Middleware\AddReportingEndpointsHeader;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ReportingApiServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('reporting-api')
            ->hasConfigFile()
            ->hasRoutes('reporting-api');
    }

    public function packageBooted(): void
    {
        $this->app['router']->aliasMiddleware('reporting-endpoints', AddReportingEndpointsHeader::class);
    }
}
