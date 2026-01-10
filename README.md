# Laravel API Response Helpers

A Laravel package for standardized API responses with helpful exceptions.

## Installation

Install the package via composer:

```bash
composer require NurbekJummayev/laravel-api-response-helpers
```

## Usage

### Helper Functions

The package provides convenient helper functions for common HTTP responses:

#### Success Responses

```php
// 200 OK
return okResponse(['users' => $users], 'Success');

// 201 Created
return createdResponse(['user' => $user], 'User created successfully');

// Paginated response
return okWithPaginateResponse($users->paginate());
```

#### Error Responses

```php
// 400 Bad Request
return badRequestResponse('Invalid input');

// 401 Unauthorized
return unauthorizedRequestResponse('Authentication required');

// 403 Forbidden
return forbiddenRequestResponse('Access denied');

// 404 Not Found
return notFoundRequestResponse('User not found');

// 422 Validation Error
return invalidData('Validation failed', ['errors' => $validator->errors()]);

// 429 Too Many Requests
return tooManyRequestsResponse('Rate limit exceeded');

// 500 Server Error
return serverErrorResponse('Something went wrong');
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
use NurbekJummayev\ApiResponseHelper\Exceptions\BadRequestException;
use NurbekJummayev\ApiResponseHelper\Exceptions\ValidationException;

// Throw exceptions
throw new NotFoundException('User not found', ['user_id' => 123]);

throw new BadRequestException('Invalid data');

throw new ValidationException(
    message: 'Validation failed',
    data: ['errors' => $validator->errors()]
);
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

```json
{
  "msg": "Success message",
  "error": null,
  "success": true,
  "data": {},
  "meta": {}
}
```

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