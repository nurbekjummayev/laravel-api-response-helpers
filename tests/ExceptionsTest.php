<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use NurbekJummayev\ApiResponseHelper\Exceptions\ApiResponseException;
use NurbekJummayev\ApiResponseHelper\Exceptions\BadRequestException;
use NurbekJummayev\ApiResponseHelper\Exceptions\ForbiddenException;
use NurbekJummayev\ApiResponseHelper\Exceptions\MethodNotAllowedException;
use NurbekJummayev\ApiResponseHelper\Exceptions\NotFoundException;
use NurbekJummayev\ApiResponseHelper\Exceptions\PostTooLargeException;
use NurbekJummayev\ApiResponseHelper\Exceptions\ServerErrorException;
use NurbekJummayev\ApiResponseHelper\Exceptions\TooManyRequestsException;
use NurbekJummayev\ApiResponseHelper\Exceptions\UnauthorizedException;
use NurbekJummayev\ApiResponseHelper\Exceptions\ValidationException;
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

it('throws server error exception', function () {
    $exception = new ServerErrorException;

    expect($exception->getCode())->toBe(Response::HTTP_INTERNAL_SERVER_ERROR)
        ->and($exception->getMessage())->toBe('Something went wrong');
});

it('throws too many requests exception', function () {
    $exception = new TooManyRequestsException;

    expect($exception->getCode())->toBe(Response::HTTP_TOO_MANY_REQUESTS);
});

it('throws method not allowed exception', function () {
    $exception = new MethodNotAllowedException;

    expect($exception->getCode())->toBe(Response::HTTP_METHOD_NOT_ALLOWED);
});

it('throws post too large exception', function () {
    $exception = new PostTooLargeException;

    expect($exception->getCode())->toBe(Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
});

it('defaults message to http status text when omitted', function () {
    expect((new NotFoundException)->getMessage())->toBe('Not Found')
        ->and((new UnauthorizedException)->getMessage())->toBe('Unauthorized')
        ->and((new BadRequestException)->getMessage())->toBe('Bad Request');
});

it('renders error message into error key', function () {
    $exception = new BadRequestException('Invalid data', errorMsg: 'ERR_INVALID');
    $response = $exception->render();

    expect($response->getData(true)['error'])->toBe('ERR_INVALID');
});

it('renders thrown exception as json over http', function () {
    Route::get('/_test/not-found', function () {
        throw new NotFoundException('User not found', ['user_id' => 123]);
    });

    $this->getJson('/_test/not-found')
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJson([
            'msg' => 'User not found',
            'success' => false,
            'data' => ['user_id' => 123],
        ]);
});

it('renders thrown validation exception as json over http', function () {
    Route::post('/_test/validate', function () {
        throw new ValidationException(data: ['errors' => ['email' => 'Required']]);
    });

    $this->postJson('/_test/validate')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJson([
            'msg' => 'The given data was invalid.',
            'success' => false,
        ]);
});
