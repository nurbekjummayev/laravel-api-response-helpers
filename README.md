# Laravel API Response Helpers

A Laravel package for standardized API responses with helpful exceptions.

## Installation

Install the package via composer:

```bash
composer require nurbekjummayev/laravel-api-response-helpers
```

## Usage

### Helper Functions

The package provides convenient helper functions for common HTTP responses:

#### Success Responses

```php
// List with pagination
public function index(Request $request)
{
    $query = Product::query();
    $data = $query->paginate($request->get('per_page', 10));

    return okWithPaginateResponse($data);
}

// Show single resource
public function show(int $id)
{
    $model = Product::findOrFail($id);

    return okResponse($model);
}

// Create resource
public function store(Request $request)
{
    $model = Product::create($request->all());

    return createdResponse($model);
}

// Update resource
public function update(Request $request, int $id)
{
    $model = Product::findOrFail($id);
    $model->update($request->all());

    return okResponse($model);
}

// Delete resource
public function destroy(int $id)
{
    $model = Product::findOrFail($id);
    $model->delete();

    return okResponse($model);
}
```

#### Error Responses

```php
// Validation error (422)
$validator = Validator::make($request->all(), $rules);
if ($validator->fails()) {
    return invalidData('Validation failed', ['errors' => $validator->errors()]);
}

// Not found (404)
$model = Product::find($id);
if (!$model) {
    return notFoundRequestResponse('Product not found');
}

// Unauthorized (401)
if (!auth()->check()) {
    return unauthorizedRequestResponse();
}

// Forbidden (403)
if (!auth()->user()->can('update', $model)) {
    return forbiddenRequestResponse('Access denied');
}

// Bad request (400)
if (!$request->has('required_field')) {
    return badRequestResponse('Missing required field');
}

// Server error (500)
try {
    // Some operation
} catch (\Exception $e) {
    return serverErrorResponse('Something went wrong');
}
```

#### Custom Response

```php
return apiResponse(
    msg: 'Custom message',
    data: ['key' => 'value'],
    success: true,
    httpStatus: 200,
    errorMsg: null,
    extraData: ['meta' => ['version' => '1.0']]
);
```

### Exceptions

The package provides exception classes that automatically render as JSON responses:

```php
use NurbekJummayev\ApiResponseHelper\Exceptions\NotFoundException;
use NurbekJummayev\ApiResponseHelper\Exceptions\ForbiddenException;
use NurbekJummayev\ApiResponseHelper\Exceptions\ValidationException;

// Not Found Exception
public function show(int $id)
{
    $model = Product::find($id);

    if (!$model) {
        throw new NotFoundException('Product not found', ['product_id' => $id]);
    }

    return okResponse($model);
}

// Validation Exception
public function store(Request $request)
{
    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        throw new ValidationException(
            message: 'Validation failed',
            data: ['errors' => $validator->errors()]
        );
    }

    $model = Product::create($request->all());
    return createdResponse($model);
}

// Forbidden Exception
public function update(Request $request, int $id)
{
    $model = Product::findOrFail($id);

    if (!auth()->user()->can('update', $model)) {
        throw new ForbiddenException('You cannot update this product');
    }

    $model->update($request->all());
    return okResponse($model);
}
```

#### Available Exceptions

- `ApiResponseException` - Base exception class
- `BadRequestException` - 400
- `UnauthorizedException` - 401
- `ForbiddenException` - 403
- `NotFoundException` - 404
- `ValidationException` - 422
- `MethodNotAllowedException` - 405
- `TooManyRequestsException` - 429
- `ServerErrorException` - 500

### Response Format

All responses follow a consistent structure:

**Standard Response:**
```json
{
  "msg": "Success message",
  "error": null,
  "success": true,
  "data": {}
}
```

**Paginated Response:**
```json
{
  "msg": "OK",
  "error": null,
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Product 1"
    },
    {
      "id": 2,
      "name": "Product 2"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 15,
    "to": 15,
    "total": 75
  }
}
```

**With Extra Data:**
```json
{
  "msg": "Success",
  "error": null,
  "success": true,
  "data": {},
  "custom_key": "custom_value"
}
```

## AI Support (Laravel Boost)

This package ships first-class AI support for [Laravel Boost](https://laravel.com/docs/boost). When a project that uses Boost installs this package, the guidelines and an agent skill are discovered automatically.

What's included:

- **Guideline** — `resources/boost/guidelines/core.blade.php`: a short, always-loaded overview of the response envelope and helpers.
- **Skill** — `resources/boost/skills/api-response-helper/SKILL.md`: the full `api-response-helper` skill, loaded on demand when an agent works on API responses.

In a consuming project that already has Boost installed:

```bash
composer require nurbekjummayev/laravel-api-response-helpers

# Discover and publish this package's guidelines + skill
php artisan boost:install
# or, for an existing Boost setup:
php artisan boost:update --discover
```

Boost then teaches the coding agent (Claude Code, Cursor, Copilot, etc.) to use `okResponse()`, `okWithPaginateResponse()`, the error helpers, and the renderable exceptions correctly. No configuration is required — discovery is based on the `resources/boost/` directory.

## Testing

```bash
composer test
```

## Code Quality

```bash
# Format code
composer format

# Test coverage
composer test-coverage
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.