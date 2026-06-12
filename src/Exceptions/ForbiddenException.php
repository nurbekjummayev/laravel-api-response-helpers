<?php

declare(strict_types=1);

namespace NurbekJummayev\ApiResponseHelper\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ForbiddenException extends ApiResponseException
{
    public function __construct(
        ?string $message = null,
        mixed $data = null,
        ?string $errorMsg = null,
        array $extraData = [],
        ?Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            data: $data,
            httpStatus: Response::HTTP_FORBIDDEN,
            errorMsg: $errorMsg,
            extraData: $extraData,
            previous: $previous,
        );
    }
}
