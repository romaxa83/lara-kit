<?php

namespace App\Modules\Permissions\Dto;

class RoleEditDto
{
    /** @var array<RoleTranslationDto> */
    protected array $translations = [];

    protected array $permissions;
    protected bool $permissionsAsKey = false;

    public string $name;

    public static function byArgs(array $args): self
    {
        $self = new self();

        $self->name = $args['name'];
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
