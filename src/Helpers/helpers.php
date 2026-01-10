<?php

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

function createdResponse($data = null, ?string $msg = null, array $extraData = []): JsonResponse
{
    return apiResponse(
        msg: $msg,
        data: $data,
        httpStatus: Response::HTTP_CREATED,
        extraData: $extraData,
    );
}

function okResponse($data = null, ?string $msg = null, array $extraData = []): JsonResponse
{
    return apiResponse(
        msg: $msg,
        data: $data,
        extraData: $extraData,
    );
}

function okWithPaginateResponse($data = null, array $extraData = []): JsonResponse
{
    // Extract pagination data
    if ($data && method_exists($data, 'items')) {
        $items = $data->items();
        $meta = [
            'current_page' => $data->currentPage(),
            'from' => $data->firstItem(),
            'last_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
            'to' => $data->lastItem(),
            'total' => $data->total(),
        ];

        return apiResponse(
            data: $items,
            extraData: array_merge(['meta' => $meta], $extraData),
        );
    }

    return apiResponse(
        data: $data,
        extraData: $extraData,
    );
}

function badRequestResponse(?string $msg = null, $data = null, string $errorMsg = '', array $extraData = []): JsonResponse
{
    return apiResponse(
        msg: $msg,
        data: $data,
        success: false,
        httpStatus: Response::HTTP_BAD_REQUEST,
        errorMsg: $errorMsg,
        extraData: $extraData,
    );
}

function invalidData(string $msg = 'The given data was invalid.', $data = null, array $extraData = []): JsonResponse
{
    return apiResponse(
        msg: $msg,
        data: $data,
        success: false,
        httpStatus: Response::HTTP_UNPROCESSABLE_ENTITY,
        extraData: $extraData,
    );
}

function unauthorizedRequestResponse(?string $msg = null, $data = null, array $extraData = []): JsonResponse
{
    return apiResponse(
        msg: $msg,
        data: $data,
        success: false,
        httpStatus: Response::HTTP_UNAUTHORIZED,
        extraData: $extraData,
    );
}

function forbiddenRequestResponse(?string $msg = null, $data = null, array $extraData = []): JsonResponse
{
    return apiResponse(
        msg: $msg,
        data: $data,
        success: false,
        httpStatus: Response::HTTP_FORBIDDEN,
        extraData: $extraData,
    );
}

function notFoundRequestResponse(?string $msg = null, $data = null, array $extraData = []): JsonResponse
{
    return apiResponse(
        msg: $msg,
        data: $data,
        success: false,
        httpStatus: Response::HTTP_NOT_FOUND,
        extraData: $extraData,
    );
}

function methodNotAllowedRequestResponse(?string $msg = null, $data = null, array $extraData = []): JsonResponse
{
    return apiResponse(
        msg: $msg,
        data: $data,
        success: false,
        httpStatus: Response::HTTP_METHOD_NOT_ALLOWED,
        extraData: $extraData,
    );
}

function tooManyRequestsResponse(?string $msg = null, $data = null, array $extraData = []): JsonResponse
{
    return apiResponse(
        msg: $msg,
        data: $data,
        success: false,
        httpStatus: Response::HTTP_TOO_MANY_REQUESTS,
        extraData: $extraData,
    );
}

function serverErrorResponse(string $msg = 'Something went wrong', $data = null, ?string $errorMsg = null, array $extraData = []): JsonResponse
{
    return apiResponse(
        msg: $msg,
        data: $data,
        success: false,
        httpStatus: Response::HTTP_INTERNAL_SERVER_ERROR,
        errorMsg: $errorMsg,
        extraData: $extraData,
    );
}

function errorResponse(string $msg = 'Something went wrong', $data = null, ?int $httpStatus = Response::HTTP_INTERNAL_SERVER_ERROR, ?string $errorMsg = null, array $extraData = []): JsonResponse
{
    return apiResponse(
        msg: $msg,
        data: $data,
        success: false,
        httpStatus: $httpStatus,
        errorMsg: $errorMsg,
        extraData: $extraData,
    );
}

function postTooLargeResponse(?string $msg = null, $data = null, array $extraData = []): JsonResponse
{
    return apiResponse(
        msg: $msg,
        data: $data,
        success: false,
        httpStatus: Response::HTTP_REQUEST_ENTITY_TOO_LARGE,
        extraData: $extraData,
    );
}

function apiResponse(?string $msg = null, $data = [], bool $success = true, $httpStatus = Response::HTTP_OK, ?string $errorMsg = null, array $extraData = []): JsonResponse
{
    return new JsonResponse([
        'msg' => $msg ?: (Response::$statusTexts[$httpStatus] ?? ''),
        'error' => $errorMsg,
        'success' => $success,
        'data' => $data,
        ...$extraData,
    ], $httpStatus);
}
