<?php

namespace audunru\ReportingApi;

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
}
