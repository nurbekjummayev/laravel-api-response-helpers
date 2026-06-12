<?php

declare(strict_types=1);

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

if (! function_exists('createdResponse')) {
    function createdResponse(mixed $data = null, ?string $msg = null, array $extraData = []): JsonResponse
    {
        return apiResponse(
            msg: $msg,
            data: $data,
            httpStatus: Response::HTTP_CREATED,
            extraData: $extraData,
        );
    }
}

if (! function_exists('okResponse')) {
    function okResponse(mixed $data = null, ?string $msg = null, array $extraData = []): JsonResponse
    {
        return apiResponse(
            msg: $msg,
            data: $data,
            extraData: $extraData,
        );
    }
}

if (! function_exists('okWithPaginateResponse')) {
    function okWithPaginateResponse(mixed $data = null, array $extraData = []): JsonResponse
    {
        if ($data instanceof LengthAwarePaginator) {
            $meta = [
                'current_page' => $data->currentPage(),
                'from' => $data->firstItem(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'to' => $data->lastItem(),
                'total' => $data->total(),
            ];
        } elseif ($data instanceof Paginator) {
            $meta = [
                'current_page' => $data->currentPage(),
                'from' => $data->firstItem(),
                'per_page' => $data->perPage(),
                'to' => $data->lastItem(),
                'has_more' => $data->hasMorePages(),
            ];
        } elseif ($data instanceof CursorPaginator) {
            $meta = [
                'per_page' => $data->perPage(),
                'next_cursor' => $data->nextCursor()?->encode(),
                'prev_cursor' => $data->previousCursor()?->encode(),
            ];
        } else {
            return apiResponse(
                data: $data,
                extraData: $extraData,
            );
        }

        return apiResponse(
            data: $data->items(),
            extraData: array_merge(['meta' => $meta], $extraData),
        );
    }
}

if (! function_exists('badRequestResponse')) {
    function badRequestResponse(?string $msg = null, mixed $data = null, string $errorMsg = '', array $extraData = []): JsonResponse
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
}

if (! function_exists('invalidData')) {
    function invalidData(string $msg = 'The given data was invalid.', mixed $data = null, array $extraData = []): JsonResponse
    {
        return apiResponse(
            msg: $msg,
            data: $data,
            success: false,
            httpStatus: Response::HTTP_UNPROCESSABLE_ENTITY,
            extraData: $extraData,
        );
    }
}

if (! function_exists('unauthorizedRequestResponse')) {
    function unauthorizedRequestResponse(?string $msg = null, mixed $data = null, array $extraData = []): JsonResponse
    {
        return apiResponse(
            msg: $msg,
            data: $data,
            success: false,
            httpStatus: Response::HTTP_UNAUTHORIZED,
            extraData: $extraData,
        );
    }
}

if (! function_exists('forbiddenRequestResponse')) {
    function forbiddenRequestResponse(?string $msg = null, mixed $data = null, array $extraData = []): JsonResponse
    {
        return apiResponse(
            msg: $msg,
            data: $data,
            success: false,
            httpStatus: Response::HTTP_FORBIDDEN,
            extraData: $extraData,
        );
    }
}

if (! function_exists('notFoundRequestResponse')) {
    function notFoundRequestResponse(?string $msg = null, mixed $data = null, array $extraData = []): JsonResponse
    {
        return apiResponse(
            msg: $msg,
            data: $data,
            success: false,
            httpStatus: Response::HTTP_NOT_FOUND,
            extraData: $extraData,
        );
    }
}

if (! function_exists('methodNotAllowedRequestResponse')) {
    function methodNotAllowedRequestResponse(?string $msg = null, mixed $data = null, array $extraData = []): JsonResponse
    {
        return apiResponse(
            msg: $msg,
            data: $data,
            success: false,
            httpStatus: Response::HTTP_METHOD_NOT_ALLOWED,
            extraData: $extraData,
        );
    }
}

if (! function_exists('tooManyRequestsResponse')) {
    function tooManyRequestsResponse(?string $msg = null, mixed $data = null, array $extraData = []): JsonResponse
    {
        return apiResponse(
            msg: $msg,
            data: $data,
            success: false,
            httpStatus: Response::HTTP_TOO_MANY_REQUESTS,
            extraData: $extraData,
        );
    }
}

if (! function_exists('serverErrorResponse')) {
    function serverErrorResponse(string $msg = 'Something went wrong', mixed $data = null, ?string $errorMsg = null, array $extraData = []): JsonResponse
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
}

if (! function_exists('errorResponse')) {
    function errorResponse(string $msg = 'Something went wrong', mixed $data = null, int $httpStatus = Response::HTTP_INTERNAL_SERVER_ERROR, ?string $errorMsg = null, array $extraData = []): JsonResponse
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
}

if (! function_exists('postTooLargeResponse')) {
    function postTooLargeResponse(?string $msg = null, mixed $data = null, array $extraData = []): JsonResponse
    {
        return apiResponse(
            msg: $msg,
            data: $data,
            success: false,
            httpStatus: Response::HTTP_REQUEST_ENTITY_TOO_LARGE,
            extraData: $extraData,
        );
    }
}

if (! function_exists('apiResponse')) {
    function apiResponse(?string $msg = null, mixed $data = null, bool $success = true, int $httpStatus = Response::HTTP_OK, ?string $errorMsg = null, array $extraData = []): JsonResponse
    {
        // The envelope keys are canonical — extraData may only add new top-level keys.
        return new JsonResponse([
            'msg' => $msg ?: (Response::$statusTexts[$httpStatus] ?? ''),
            'error' => $errorMsg,
            'success' => $success,
            'data' => $data,
            ...array_diff_key($extraData, array_flip(['msg', 'error', 'success', 'data'])),
        ], $httpStatus);
    }
}
