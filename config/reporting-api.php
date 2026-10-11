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
];
