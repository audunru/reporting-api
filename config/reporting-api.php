<?php

return [
    /*
     * The path where the reporting endpoint will be registered.
     */
    'path' => env('REPORTING_API_PATH', '/reports'),

    /*
     * The throttle middleware value applied to the report endpoint.
     * Can be a named rate limiter (e.g. 'log') or attempts:minutes format (e.g. '60,1').
     */
    'throttle' => env('REPORTING_API_THROTTLE', '60,1'),

    /*
     * The maximum size of the request body in bytes. Larger bodies are rejected
     * with 413 Payload Too Large before they are decoded.
     */
    'max_body_size' => (int) env('REPORTING_API_MAX_BODY_SIZE', 65536),

    /*
     * The maximum number of reports in one application/reports+json request.
     * Requests with more reports are rejected with 413 Payload Too Large.
     */
    'max_reports' => (int) env('REPORTING_API_MAX_REPORTS', 50),
];
