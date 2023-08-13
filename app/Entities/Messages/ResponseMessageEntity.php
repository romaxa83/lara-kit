<?php

namespace App\Entities\Messages;

class ResponseMessageEntity
{
    private function __construct(
        public string $message,
        public bool $success = true
    ) {
    }

    public static function fail(string $msg): static
    {
        return new static($msg, false);
    }

    public static function success(string $msg): static
    {
        return new static($msg);
    }
}
