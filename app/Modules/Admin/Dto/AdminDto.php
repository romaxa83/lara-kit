<?php

namespace App\Modules\Admin\Dto;

use App\Modules\Permissions\Models\Role;
use App\Modules\Utils\Phones\Dto\PhoneDto;
use App\Modules\Utils\Phones\Dto\PhonesDto;
use App\Modules\Utils\Phones\ValueObject\Phone;
use App\ValueObjects\Email;

final class AdminDto
{
    public string $name;
    public Email $email;
    public ?string $password;
    public Role|int|string $role;
    public ?string $lang;

    public ?PhoneDto $phoneDto;
    public ?PhonesDto $phonesDto;

    public static function byArgs(array $args): self
    {
        $self = new self();

        $self->name = $args['name'];
        $self->email = new Email($args['email']);

        $self->password = $args['password'] ?? null;
        $self->role = $args['role'];
        $self->lang = $args['lang'] ?? null;

        $self->phoneDto = is_contains($args, 'phone')
            ? PhoneDto::byArgs([
                'phone' => new Phone($args['phone']),
                'default' => true
            ])
            : null;
        $self->phonesDto = is_contains($args, 'phones')
            ? PhonesDto::byArgs(data_get($args, 'phones', []))
            : null
        ;

        return $self;
    }
}
