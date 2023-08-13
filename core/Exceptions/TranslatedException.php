<?php

declare(strict_types=1);

namespace Core\Exceptions;

use GraphQL\Error\ClientAware;
use GraphQL\Error\Error;
use RuntimeException;

class TranslatedException extends RuntimeException implements ClientAware
{
    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return Error::CATEGORY_INTERNAL;
    }
}
