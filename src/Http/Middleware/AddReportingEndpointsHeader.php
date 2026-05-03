<?php

namespace audunru\ReportingApi\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddReportingEndpointsHeader
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $path = config('reporting-api.path', '/reports');
        $response->headers->set('Reporting-Endpoints', 'default="'.$path.'"');

        return $response;
    }
}
