<?php


use Illuminate\Support\Facades\Log;

if (!function_exists('log_error')) {
    function log_error($error, array $context)
    {
        \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($error, $context): void {
            if (auth()->check()) {
                $scope->setContext('user', [
                    'id'    => auth()->id(),
                    'email' => auth()->user()->user_email
                ]);
            }

            $eventId = \Sentry\captureException($error);
            Log::error($error, ['eventId' => $eventId, 'context' => $context]);
        });
    }
}
