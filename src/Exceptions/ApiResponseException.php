<?php

namespace Nurbekjummayev\ApiResponseHelper\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiResponseException extends Exception
{
    protected mixed $data;

    protected array $extraData;

    protected ?string $errorMsg;

    public function __construct(
        ?string $message = null,
        mixed $data = null,
        int $httpStatus = Response::HTTP_BAD_REQUEST,
        ?string $errorMsg = null,
        array $extraData = [],
        ?Throwable $previous = null
    ) {
        $this->data = $data;
        $this->errorMsg = $errorMsg;
        $this->extraData = $extraData;

        parent::__construct(
            $message ?: (Response::$statusTexts[$httpStatus] ?? 'Error'),
            $httpStatus,
            $previous
        );
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        //
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(): JsonResponse
    {
        return apiResponse(
            msg: $this->getMessage(),
            data: $this->data,
            success: false,
            httpStatus: $this->getCode(),
            errorMsg: $this->errorMsg,
            extraData: $this->extraData,
        );
    }
}
