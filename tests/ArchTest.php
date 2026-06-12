<?php

declare(strict_types=1);

arch('source files use strict types')
    ->expect('NurbekJummayev\ApiResponseHelper')
    ->toUseStrictTypes();

arch('exceptions extend the base api exception')
    ->expect('NurbekJummayev\ApiResponseHelper\Exceptions')
    ->classes()
    ->toExtend(Exception::class);

arch('no debug statements are left behind')
    ->expect(['dd', 'dump', 'ray', 'var_dump'])
    ->not->toBeUsed();
