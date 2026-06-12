<?php

declare(strict_types=1);

namespace NurbekJummayev\ApiResponseHelper\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ServerErrorException extends ApiResponseException
{
    public function __construct(
        string $message = 'Something went wrong',
        mixed $data = null,
        ?string $errorMsg = null,
        array $extraData = [],
        ?Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            data: $data,
            httpStatus: Response::HTTP_INTERNAL_SERVER_ERROR,
            errorMsg: $errorMsg,
            extraData: $extraData,
            previous: $previous,
        );
    }
}
