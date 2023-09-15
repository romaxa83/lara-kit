<?php

namespace App\Modules\Utils\Phones\Dto;

use App\Modules\Utils\Phones\ValueObject\Phone;

final class PhoneDto
{
    public Phone $phone;
    public bool $default;
    public bool $verify;
    public ?string $desc;

    public static function byArgs(array $args): self
    {
        $self = new self();

        $self->phone = new Phone($args['phone']);
        $self->default = $args['default'];
        $self->desc = $args['desc'] ?? null;
        $self->verify = $args['verify'] ?? false;

        return $self;
    }
}
