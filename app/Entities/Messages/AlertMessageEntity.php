<?php

namespace App\Entities\Messages;

class AlertMessageEntity
{
    public function __construct(
        public string $message,
        public string $type,
        public ?string $target = null
    ) {
    }
}
