## Laravel API Response Helpers

This package (`nurbekjummayev/laravel-api-response-helpers`) standardizes JSON API responses through global helper functions and renderable exceptions. Every response follows the same envelope: `msg`, `error`, `success`, `data`, plus any `extraData` merged at the top level.

### Conventions

- In API controllers, ALWAYS return one of the package helper functions instead of constructing `response()->json(...)` by hand. This keeps the response envelope consistent across the whole API.
- The helpers are global functions (autoloaded via `files`), so no `use` import is needed for them.
- For error paths, prefer throwing one of the package exceptions over returning an error helper when the error should short-circuit the request — exceptions render themselves as the same JSON envelope automatically.

### Success helpers

@verbatim
<code-snippet name="Success responses" lang="php">
return okResponse($model);                       // 200
return createdResponse($model);                  // 201
return okWithPaginateResponse($paginator);       // 200 + meta{} from a paginator
</code-snippet>
@endverbatim

`okWithPaginateResponse()` expects a paginator (anything with an `items()` method) and automatically adds a `meta` block (`current_page`, `from`, `last_page`, `per_page`, `to`, `total`).

### Error helpers

@verbatim
<code-snippet name="Error responses" lang="php">
return badRequestResponse('Missing field');              // 400
return unauthorizedRequestResponse();                    // 401
return forbiddenRequestResponse('Access denied');        // 403
return notFoundRequestResponse('Product not found');     // 404
return invalidData('Validation failed', $errors);        // 422
return serverErrorResponse('Something went wrong');      // 500
return errorResponse('Custom', httpStatus: 418);         // any status
</code-snippet>
@endverbatim

### Renderable exceptions

Throw these to short-circuit a request with the standard JSON envelope (no try/catch needed — they self-render):

@verbatim
<code-snippet name="Throwing API exceptions" lang="php">
use NurbekJummayev\ApiResponseHelper\Exceptions\NotFoundException;

throw new NotFoundException('Product not found', data: ['product_id' => $id]);
</code-snippet>
@endverbatim

Available: `ApiResponseException` (base), `BadRequestException` (400), `UnauthorizedException` (401), `ForbiddenException` (403), `NotFoundException` (404), `MethodNotAllowedException` (405), `ValidationException` (422), `TooManyRequestsException` (429), `ServerErrorException` (500).

For deeper patterns and the full API, use the `api-response-helper` skill.
