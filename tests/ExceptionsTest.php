<?php

use Nurbekjummayev\ApiResponseHelper\Exceptions\ApiResponseException;
use Nurbekjummayev\ApiResponseHelper\Exceptions\BadRequestException;
use Nurbekjummayev\ApiResponseHelper\Exceptions\ForbiddenException;
use Nurbekjummayev\ApiResponseHelper\Exceptions\NotFoundException;
use Nurbekjummayev\ApiResponseHelper\Exceptions\UnauthorizedException;
use Nurbekjummayev\ApiResponseHelper\Exceptions\ValidationException;
use Symfony\Component\HttpFoundation\Response;

it('throws bad request exception', function () {
    $exception = new BadRequestException('Invalid data');

    expect($exception->getCode())->toBe(Response::HTTP_BAD_REQUEST)
        ->and($exception->getMessage())->toBe('Invalid data');
});

it('throws not found exception', function () {
    $exception = new NotFoundException('Resource not found');

    expect($exception->getCode())->toBe(Response::HTTP_NOT_FOUND)
        ->and($exception->getMessage())->toBe('Resource not found');
});

it('throws unauthorized exception', function () {
    $exception = new UnauthorizedException;

    expect($exception->getCode())->toBe(Response::HTTP_UNAUTHORIZED);
});

it('throws forbidden exception', function () {
    $exception = new ForbiddenException;

    expect($exception->getCode())->toBe(Response::HTTP_FORBIDDEN);
});

it('throws validation exception', function () {
    $exception = new ValidationException(
        message: 'Validation failed',
        data: ['errors' => ['email' => 'Required']]
    );

    expect($exception->getCode())->toBe(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->and($exception->getMessage())->toBe('Validation failed');
});

it('renders exception as json response', function () {
    $exception = new NotFoundException('User not found', ['user_id' => 123]);
    $response = $exception->render();

    expect($response->getStatusCode())->toBe(Response::HTTP_NOT_FOUND)
        ->and($response->getData(true)['success'])->toBeFalse()
        ->and($response->getData(true)['msg'])->toBe('User not found')
        ->and($response->getData(true)['data'])->toBe(['user_id' => 123]);
});

it('supports extra data in exception', function () {
    $exception = new ApiResponseException(
        message: 'Error',
        extraData: ['meta' => ['timestamp' => '2024-01-01']]
    );
    $response = $exception->render();

    expect($response->getData(true))->toHaveKey('meta');
});
