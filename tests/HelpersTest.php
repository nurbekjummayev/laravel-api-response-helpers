<?php

declare(strict_types=1);

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

    $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
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
