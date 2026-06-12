---
name: api-response-helper
description: Build standardized JSON API responses in Laravel using the laravel-api-response-helpers package — success/error helper functions, paginated responses, custom envelopes, and self-rendering API exceptions. Use when writing API controllers, formatting JSON responses, or handling API error states.
---

# Laravel API Response Helpers

Standardize every JSON response in a Laravel API behind a single, consistent envelope. The package (`nurbekjummayev/laravel-api-response-helpers`) ships global helper functions and a set of renderable exceptions so controllers never hand-build `response()->json(...)`.

## When to use this skill

Use this skill when:

- Writing or editing API controllers / API resource endpoints.
- Returning JSON from a Laravel route or controller.
- Handling API error states (validation, not found, unauthorized, forbidden, server error).
- The user wants a consistent response shape across an API.

Do **not** use it for web (Blade/Inertia) responses or for non-Laravel code.

## Response envelope

Every helper and exception renders the same top-level structure:

```json
{
  "msg": "OK",
  "error": null,
  "success": true,
  "data": {}
}
```

- `msg` — human-readable message. When omitted, it defaults to the HTTP status text (e.g. `"Not Found"`).
- `error` — optional machine/error string (`errorMsg`), `null` on success.
- `success` — `true` for 2xx helpers, `false` for error helpers and exceptions.
- `data` — the payload (model, array, collection, or `null`).
- Any `extraData` array is **spread at the top level**, so keys like `meta` sit alongside `data`, not inside it.

## Success helpers

```php
return okResponse($model);                  // 200
return okResponse($model, 'Updated');       // 200 with custom message
return createdResponse($model);             // 201 — use after creating a resource
return okWithPaginateResponse($paginator);  // 200 with auto meta{}
```

`createdResponse`, `okResponse` signature: `($data = null, ?string $msg = null, array $extraData = [])`.

### Paginated responses

`okWithPaginateResponse($data, array $extraData = [])` detects a paginator (any object exposing `items()`) and emits the items as `data` plus a `meta` block. Pass a `LengthAwarePaginator` straight from `paginate()`:

```php
public function index(Request $request)
{
    $products = Product::query()->paginate($request->integer('per_page', 10));

    return okWithPaginateResponse($products);
}
```

Produces:

```json
{
  "msg": "OK",
  "error": null,
  "success": true,
  "data": [ { "id": 1, "name": "Product 1" } ],
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

All three Laravel paginators are supported:

- `paginate()` (`LengthAwarePaginator`) — full `meta` as shown above.
- `simplePaginate()` (`Paginator`) — `meta` with `current_page`, `from`, `per_page`, `to`, `has_more` (no `total`/`last_page`).
- `cursorPaginate()` (`CursorPaginator`) — `meta` with `per_page`, `next_cursor`, `prev_cursor`.

If the passed value is not a paginator, it falls back to returning it directly as `data` (no `meta`).

## Error helpers

All set `success: false` and the matching HTTP status:

```php
return badRequestResponse('Missing required field');           // 400
return unauthorizedRequestResponse();                          // 401
return forbiddenRequestResponse('Access denied');              // 403
return notFoundRequestResponse('Product not found');           // 404
return methodNotAllowedRequestResponse();                      // 405
return invalidData('Validation failed', $validator->errors()); // 422
return tooManyRequestsResponse();                              // 429
return postTooLargeResponse();                                 // 413
return serverErrorResponse('Something went wrong');            // 500
```

For an arbitrary status, use `errorResponse`:

```php
return errorResponse('I am a teapot', httpStatus: 418, errorMsg: 'TEAPOT');
```

Common error helper signatures:

- `badRequestResponse(?string $msg = null, $data = null, string $errorMsg = '', array $extraData = [])`
- `invalidData(string $msg = 'The given data was invalid.', $data = null, array $extraData = [])`
- `serverErrorResponse(string $msg = 'Something went wrong', $data = null, ?string $errorMsg = null, array $extraData = [])`
- `errorResponse(string $msg = 'Something went wrong', $data = null, ?int $httpStatus = 500, ?string $errorMsg = null, array $extraData = [])`

## Custom envelope

When you need full control, call `apiResponse` directly:

```php
return apiResponse(
    msg: 'Custom message',
    data: ['key' => 'value'],
    success: true,
    httpStatus: 200,
    errorMsg: null,
    extraData: ['meta' => ['version' => '1.0']],
);
```

## Renderable exceptions

Prefer throwing an exception when an error should short-circuit the request — each one self-renders into the same JSON envelope via its `render()` method, so no try/catch and no manual return are needed.

```php
use NurbekJummayev\ApiResponseHelper\Exceptions\NotFoundException;
use NurbekJummayev\ApiResponseHelper\Exceptions\ForbiddenException;
use NurbekJummayev\ApiResponseHelper\Exceptions\ValidationException;

public function show(int $id)
{
    $product = Product::find($id);

    if (! $product) {
        throw new NotFoundException('Product not found', data: ['product_id' => $id]);
    }

    return okResponse($product);
}

public function update(Request $request, int $id)
{
    $product = Product::findOrFail($id);

    if (! $request->user()->can('update', $product)) {
        throw new ForbiddenException('You cannot update this product');
    }

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        throw new ValidationException('Validation failed', data: $validator->errors());
    }

    $product->update($request->validated());

    return okResponse($product);
}
```

Constructor signature (shared across all subclasses; the base also accepts `httpStatus`):

```php
new NotFoundException(
    ?string $message = null,
    mixed $data = null,
    ?string $errorMsg = null,
    array $extraData = [],
    ?Throwable $previous = null,
);
```

### Available exceptions

| Exception                    | Status |
| ---------------------------- | ------ |
| `ApiResponseException`       | base (default 400, accepts custom `httpStatus`) |
| `BadRequestException`        | 400    |
| `UnauthorizedException`      | 401    |
| `ForbiddenException`         | 403    |
| `NotFoundException`          | 404    |
| `MethodNotAllowedException`  | 405    |
| `PostTooLargeException`      | 413    |
| `ValidationException`        | 422    |
| `TooManyRequestsException`   | 429    |
| `ServerErrorException`       | 500    |

This package's `ValidationException` shares its short name with Laravel's `Illuminate\Validation\ValidationException` — alias the import when both are needed in one file.

All exception classes live under the `NurbekJummayev\ApiResponseHelper\Exceptions` namespace.

## Best practices

- One envelope everywhere: never mix raw `response()->json()` with these helpers in the same API.
- Use `createdResponse()` (201) for store actions, `okResponse()` (200) for show/update/destroy.
- Use `okWithPaginateResponse()` for any list endpoint backed by `paginate()` — don't manually build `meta`.
- Throw exceptions for guard clauses (auth, ownership, existence); return error helpers when you want to continue execution afterward.
- Put cross-cutting metadata (versioning, request id, etc.) in `extraData` so it lands at the top level alongside `data`. The envelope keys (`msg`, `error`, `success`, `data`) are protected — `extraData` cannot overwrite them.
- The helper functions are global (autoloaded) — call them directly without an import; only exceptions need a `use` statement.
