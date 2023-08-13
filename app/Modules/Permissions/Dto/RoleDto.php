<?php

namespace App\Modules\Permissions\Dto;

use App\Modules\Permissions\Enums\Guard;

class RoleDto
{
    /** @var array<RoleTranslationDto> */
    protected array $translations = [];

    protected array $permissions;
    protected bool $permissionsAsKey = false;

    public string $name;
    public string $guard;

    public static function byArgs(array $args): self
    {
        $self = new self();

        $self->name = $args['name'];
        $self->guard = $args['guard'] ?? GUARD::USER;
        $self->permissions = $args['permissions'] ?? [];
        $self->permissionsAsKey = $args['permissions_as_key'] ?? false;

        foreach ($args['translations'] as $translation) {
            $self->translations[] = RoleTranslationDto::byArgs($translation);
        }

        return $self;
    }

    public function getTranslations(): array
    {
        return $this->translations;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function isPermissionsAsKey(): bool
    {
        return $this->permissionsAsKey;
    }
}

