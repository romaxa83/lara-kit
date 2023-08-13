<?php

namespace App\Modules\Admin\Dto;

use App\Modules\Permissions\Models\Role;
use App\Modules\Utils\Phones\ValueObject\Phone;
use App\ValueObjects\Email;

final class AdminDto
{
    public string $name;
    public Email $email;
    public ?Phone $phone;
    public ?string $password;
    public Role|int|string $role;
    public ?string $lang;

    public static function byArgs(array $args): self
    {
        $self = new self();

        $self->name = $args['name'];
        $self->email = new Email($args['email']);
        $self->phone = is_contains($args, 'phone') ? new Phone($args['phone']) : null;
        $self->password = $args['password'] ?? null;
        $self->role = $args['role'];
        $self->lang = $args['lang'] ?? null;

        return $self;
    }
}
