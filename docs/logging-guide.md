# Logging Guide

## Overview

This application uses a standardized logging system that outputs logs in JSON string format. All logs are structured and include contextual information for better debugging and monitoring.

## Logging Format

All logs are stored in JSON format with the following structure:

```json
{
  "timestamp": "2025-01-04T10:30:45+00:00",
  "action": "auth_login_success",
  "data": {
    "email": "user@example.com",
    "remember": true
  },
  "request": {
    "ip": "192.168.1.1",
    "url": "https://example.com/login",
    "method": "POST",
    "user_agent": "Mozilla/5.0..."
  },
  "user": {
    "id": 1,
    "email": "user@example.com",
    "roles": ["participant"]
  }
}
```

## Logger Helper Class

Location: `app/Helpers/Logger.php`

### Available Methods

#### 1. `log(string $level, string $action, array $data, ?Request $request)`
General-purpose logging method.

**Parameters:**
- `$level`: Log level ('info', 'warning', 'error', 'debug')
- `$action`: Action identifier (e.g., 'user_created', 'login_success')
- `$data`: Additional data to log
- `$request`: Current request instance (optional)

**Example:**
```php
use App\Helpers\Logger;

Logger::log('info', 'custom_action', [
    'key' => 'value',
    'another_key' => 123,
], request());
```

#### 2. `logAuth(string $action, array $data, ?Request $request)`
Authentication-specific logging.

**Actions:**
- `form_viewed` - When login form is viewed
- `attempt` - Login attempt
- `success` - Successful login
- `failed` - Failed login
- `logout_attempt` - Logout attempt
- `logout_success` - Successful logout
- `register_attempt` - Registration attempt
- `register_success` - Successful registration

**Example:**
```php
Logger::logAuth('success', [
    'email' => $request->email,
    'remember' => true,
], $request);
```

#### 3. `logRequest(Request $request, array $additionalData)`
Log incoming API requests.

**Example:**
```php
Logger::logRequest($request, [
    'custom_field' => 'custom_value',
]);
```

#### 4. `logDb(string $operation, string $model, array $data)`
Log database operations.

**Operations:**
- `create`
- `update`
- `delete`

**Example:**
```php
Logger::logDb('create', 'User', [
    'user_id' => $user->id,
    'email' => $user->email,
]);
```

#### 5. `logError(string $action, \Exception $exception, array $additionalData, ?Request $request)`
Log errors with exception details.

**Example:**
```php
try {
    // Some code
} catch (\Exception $e) {
    Logger::logError('action_failed', $e, [
        'context' => 'additional context',
    ], request());
}
```

## Middleware

### LogRequests Middleware

Location: `app/Http/Middleware/LogRequests.php`

Automatically logs all web requests with:
- Route name
- HTTP method
- Path
- Response status code
- Request duration (ms)
- AJAX flag

The middleware is automatically applied to all web routes via `bootstrap/app.php`.

## Usage Examples

### In Controllers

```php
use App\Helpers\Logger;

class MyController extends Controller
{
    public function store(Request $request)
    {
        Logger::log('info', 'item_creation_attempt', [
            'type' => $request->type,
        ], $request);

        try {
            $item = Item::create($request->validated());

            Logger::logDb('create', 'Item', [
                'item_id' => $item->id,
                'name' => $item->name,
            ]);

            Logger::log('info', 'item_created', [
                'item_id' => $item->id,
            ], $request);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Logger::logError('item_creation_failed', $e, [
                'request_data' => $request->all(),
            ], $request);

            return response()->json(['error' => 'Creation failed'], 500);
        }
    }
}
```

### In Auth Controller

The AuthController already has comprehensive logging:

```php
// Login attempt
Logger::logAuth('attempt', [
    'email' => $request->email,
    'remember' => $request->boolean('remember'),
], $request);

// Login success
Logger::logAuth('success', [
    'email' => $request->email,
], $request);

// Login failed
Logger::logAuth('failed', [
    'email' => $request->email,
    'reason' => 'invalid_credentials',
], $request);
```

## Log Levels

- **info**: General informational messages
- **warning**: Warning messages for potentially harmful situations
- **error**: Error messages for error events
- **debug**: Debug-level messages (usually disabled in production)

## Log Storage

Logs are stored in the default Laravel log location: `storage/logs/laravel.log`

## Best Practices

1. **Use descriptive action names**: Use clear, action-oriented names like `user_login_success` instead of `success`
2. **Include relevant context**: Add relevant data that helps understand the log context
3. **Log sensitive data carefully**: Avoid logging passwords, tokens, or other sensitive information
4. **Use appropriate log levels**: Match log level to severity
5. **Log both attempts and results**: Log both the start and outcome of operations
6. **Include request context**: Always pass the request when available to get IP, URL, and user agent

## Viewing Logs

### Using Tail (Linux/Mac)

```bash
tail -f storage/logs/laravel.log
```

### Using Grep

```bash
grep "auth_login_success" storage/logs/laravel.log
```

### Using jq for JSON parsing

```bash
tail -f storage/logs/laravel.log | jq
```

## Configuration

To customize logging behavior, modify the `Logger` class in `app/Helpers/Logger.php`.

To change log storage or formatting, update Laravel's logging configuration in `config/logging.php`.
