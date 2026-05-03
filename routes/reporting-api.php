<?php

use audunru\ReportingApi\Controllers\ReportingApiController;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::post(config('reporting-api.path', '/reports'), [ReportingApiController::class, 'report'])
    ->middleware(['throttle:'.config('reporting-api.throttle', '60,1')])
    ->withoutMiddleware([VerifyCsrfToken::class, ValidateCsrfToken::class, PreventRequestForgery::class]);
