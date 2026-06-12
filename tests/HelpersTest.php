<?php

declare(strict_types=1);

use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Response;

it('returns created response', function () {
    $response = createdResponse(['id' => 1], 'Resource created');

    expect($response->getStatusCode())->toBe(Response::HTTP_CREATED)
        ->and($response->getData(true)['success'])->toBeTrue()
        ->and($response->getData(true)['msg'])->toBe('Resource created')
        ->and($response->getData(true)['data'])->toBe(['id' => 1]);
});

it('returns ok response', function () {
    $response = okResponse(['users' => []]);

    expect($response->getStatusCode())->toBe(Response::HTTP_OK)
        ->and($response->getData(true)['success'])->toBeTrue();
});

it('returns bad request response', function () {
    $response = badRequestResponse('Invalid input');

    expect($response->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST)
        ->and($response->getData(true)['success'])->toBeFalse()
        ->and($response->getData(true)['msg'])->toBe('Invalid input');
});

it('returns unauthorized response', function () {
    $response = unauthorizedRequestResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_UNAUTHORIZED)
        ->and($response->getData(true)['success'])->toBeFalse();
});

it('returns not found response', function () {
    $response = notFoundRequestResponse('User not found');

    expect($response->getStatusCode())->toBe(Response::HTTP_NOT_FOUND)
        ->and($response->getData(true)['msg'])->toBe('User not found');
});

it('returns validation error response', function () {
    $response = invalidData('Validation failed', ['errors' => ['email' => 'Required']]);

    expect($response->getStatusCode())->toBe(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->and($response->getData(true)['success'])->toBeFalse()
        ->and($response->getData(true)['data'])->toHaveKey('errors');
});

it('returns server error response', function () {
    $response = serverErrorResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_INTERNAL_SERVER_ERROR)
        ->and($response->getData(true)['success'])->toBeFalse();
});

it('supports extra data in response', function () {
    $response = okResponse(
        data: ['user' => 'John'],
        extraData: ['meta' => ['version' => '1.0']]
    );

    expect($response->getData(true))->toHaveKey('meta')
        ->and($response->getData(true)['meta'])->toBe(['version' => '1.0']);
});

it('formats paginated response correctly', function () {
    // Create a mock paginator
    $items = collect([
        ['id' => 1, 'name' => 'Item 1'],
        ['id' => 2, 'name' => 'Item 2'],
    ]);

    $paginator = new LengthAwarePaginator(
        $items,
        50, // total
        15, // per page
        1   // current page
    );

    $response = okWithPaginateResponse($paginator);
    $data = $response->getData(true);

    expect($data)->toHaveKey('data')
        ->and($data)->toHaveKey('meta')
        ->and($data['data'])->toHaveCount(2)
        ->and($data['meta'])->toHaveKey('current_page')
        ->and($data['meta']['current_page'])->toBe(1)
        ->and($data['meta']['total'])->toBe(50)
        ->and($data['meta']['per_page'])->toBe(15);
});

it('formats simple paginated response without total', function () {
    $items = collect([
        ['id' => 1, 'name' => 'Item 1'],
        ['id' => 2, 'name' => 'Item 2'],
    ]);

    $paginator = new Paginator($items, 15, 1);

    $response = okWithPaginateResponse($paginator);
    $data = $response->getData(true);

    expect($response->getStatusCode())->toBe(Response::HTTP_OK)
        ->and($data['data'])->toHaveCount(2)
        ->and($data['meta'])->toHaveKey('current_page')
        ->and($data['meta'])->toHaveKey('per_page')
        ->and($data['meta'])->toHaveKey('has_more')
        ->and($data['meta'])->not->toHaveKey('total')
        ->and($data['meta'])->not->toHaveKey('last_page');
});

it('formats cursor paginated response', function () {
    $items = collect([
        ['id' => 1, 'name' => 'Item 1'],
        ['id' => 2, 'name' => 'Item 2'],
    ]);

    $paginator = new CursorPaginator($items, 15);

    $response = okWithPaginateResponse($paginator);
    $data = $response->getData(true);

    expect($data['data'])->toHaveCount(2)
        ->and($data['meta'])->toHaveKey('per_page')
        ->and($data['meta'])->toHaveKey('next_cursor')
        ->and($data['meta'])->toHaveKey('prev_cursor');
});

it('falls back to plain data when not a paginator', function () {
    $response = okWithPaginateResponse(['id' => 1]);
    $data = $response->getData(true);

    expect($data['data'])->toBe(['id' => 1])
        ->and($data)->not->toHaveKey('meta');
});

it('returns forbidden response', function () {
    $response = forbiddenRequestResponse('Access denied');

    expect($response->getStatusCode())->toBe(Response::HTTP_FORBIDDEN)
        ->and($response->getData(true)['success'])->toBeFalse()
        ->and($response->getData(true)['msg'])->toBe('Access denied');
});

it('returns method not allowed response', function () {
    $response = methodNotAllowedRequestResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_METHOD_NOT_ALLOWED)
        ->and($response->getData(true)['success'])->toBeFalse();
});

it('returns too many requests response', function () {
    $response = tooManyRequestsResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_TOO_MANY_REQUESTS)
        ->and($response->getData(true)['success'])->toBeFalse();
});

it('returns post too large response', function () {
    $response = postTooLargeResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_REQUEST_ENTITY_TOO_LARGE)
        ->and($response->getData(true)['success'])->toBeFalse();
});

it('returns error response with custom status and error message', function () {
    $response = errorResponse('Teapot', httpStatus: Response::HTTP_I_AM_A_TEAPOT, errorMsg: 'TEAPOT');

    expect($response->getStatusCode())->toBe(Response::HTTP_I_AM_A_TEAPOT)
        ->and($response->getData(true)['msg'])->toBe('Teapot')
        ->and($response->getData(true)['error'])->toBe('TEAPOT')
        ->and($response->getData(true)['success'])->toBeFalse();
});

it('returns api response defaults', function () {
    $response = apiResponse();
    $data = $response->getData(true);

    expect($response->getStatusCode())->toBe(Response::HTTP_OK)
        ->and($data['msg'])->toBe('OK')
        ->and($data['error'])->toBeNull()
        ->and($data['success'])->toBeTrue()
        ->and($data['data'])->toBeNull();
});

it('defaults msg to http status text when omitted', function () {
    expect(notFoundRequestResponse()->getData(true)['msg'])->toBe('Not Found')
        ->and(unauthorizedRequestResponse()->getData(true)['msg'])->toBe('Unauthorized')
        ->and(createdResponse()->getData(true)['msg'])->toBe('Created');
});

it('does not allow extra data to override envelope keys', function () {
    $response = okResponse(
        data: ['user' => 'John'],
        extraData: ['success' => false, 'msg' => 'hacked', 'meta' => ['v' => 1]]
    );
    $data = $response->getData(true);

    expect($data['success'])->toBeTrue()
        ->and($data['msg'])->toBe('OK')
        ->and($data['data'])->toBe(['user' => 'John'])
        ->and($data['meta'])->toBe(['v' => 1]);
});
