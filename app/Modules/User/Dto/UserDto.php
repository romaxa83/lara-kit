<?php

namespace App\Modules\User\Dto;

use App\Modules\Utils\Phones\ValueObject\Phone;
use App\ValueObjects\Email;

final class UserDto
{
    public string $name;
    public Email $email;
    public ?Phone $phone;
    public ?string $password;
    public ?string $lang;

    public static function byArgs(array $args): self
    {
        $self = new self();

        $self->name = $args['name'];
        $self->email = new Email($args['email']);
        $self->phone = is_contains($args, 'phone') ? new Phone($args['phone']) : null;
        $self->password = $args['password'] ?? null;
        $self->lang = $args['lang'] ?? null;

        return $self;
    }
}
