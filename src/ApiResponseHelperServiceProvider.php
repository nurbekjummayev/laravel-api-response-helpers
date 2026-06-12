<?php

declare(strict_types=1);

namespace NurbekJummayev\ApiResponseHelper;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ApiResponseHelperServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('response-api-helper');
    }

    public function packageRegistered(): void {}
}
