<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class Logger
{
    /**
     * Log with standardized JSON format
     *
     * @param string $level Log level (info, warning, error, debug)
     * @param string $action Action being performed
     * @param array $data Additional data to log
     * @param Request|null $request Current request (optional)
     * @return void
     */
    public static function log(string $level, string $action, array $data = [], ?Request $request = null): void
    {
        $logData = [
            'timestamp' => now()->toIso8601String(),
            'action' => $action,
            'data' => $data,
        ];

        // Add request context if available
        if ($request) {
            $logData['request'] = [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
            ];
        }

        // Add user context if authenticated
        if (auth()->check()) {
            $logData['user'] = [
                'id' => auth()->id(),
                'email' => auth()->user()->email,
                'roles' => auth()->user()->roles->pluck('name')->toArray(),
            ];
        }

        // Convert to JSON string
        $jsonLog = json_encode($logData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // Log based on level
        switch ($level) {
            case 'info':
                Log::info($jsonLog);
                break;
            case 'warning':
                Log::warning($jsonLog);
                break;
            case 'error':
                Log::error($jsonLog);
                break;
            case 'debug':
                Log::debug($jsonLog);
                break;
            default:
                Log::info($jsonLog);
        }
    }

    /**
     * Log authentication attempt
     *
     * @param string $action Action (login_attempt, login_success, login_failed, logout, etc.)
     * @param array $data Additional data
     * @param Request $request Current request
     * @return void
     */
    public static function logAuth(string $action, array $data = [], ?Request $request = null): void
    {
        self::log('info', "auth_{$action}", $data, $request);
    }

    /**
     * Log API request
     *
     * @param Request $request Current request
     * @param array $additionalData Additional data to log
     * @return void
     */
    public static function logRequest(Request $request, array $additionalData = []): void
    {
        $data = array_merge([
            'route' => $request->route()?->getName(),
            'middleware' => $request->route()?->gatherMiddleware(),
        ], $additionalData);

        self::log('info', 'incoming_request', $data, $request);
    }

    /**
     * Log database operation
     *
     * @param string $operation Operation type (create, update, delete)
     * @param string $model Model name
     * @param array $data Model data
     * @return void
     */
    public static function logDb(string $operation, string $model, array $data = []): void
    {
        self::log('info', "db_{$operation}", [
            'model' => $model,
            'data' => $data,
        ]);
    }

    /**
     * Log error
     *
     * @param string $action Action being performed
     * @param \Exception $exception Exception object
     * @param array $additionalData Additional data
     * @param Request|null $request Current request
     * @return void
     */
    public static function logError(string $action, \Exception $exception, array $additionalData = [], ?Request $request = null): void
    {
        $data = array_merge([
            'exception' => [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => config('app.debug') ? $exception->getTraceAsString() : 'Hidden in production',
            ],
        ], $additionalData);

        self::log('error', $action, $data, $request);
    }
}
