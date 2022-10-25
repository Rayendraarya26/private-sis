<?php


use Illuminate\Support\Facades\Log;

if (!function_exists('log_error')) {
    function log_error($error, array $context)
    {
        $eventId = \Sentry\captureException($error);
        Log::error($eventId, $context);
    }
}
