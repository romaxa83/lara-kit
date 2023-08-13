<?php

namespace App\Modules\Permissions\Dto;

use App\Modules\Permissions\Enums\Guard;

final class PermissionDto
{
    /** @var array<PermissionTranslationDto> */
    private array $translations = [];

    public string $name;
    public string $guard;

    public static function byArgs(array $args): self
    {

        $self = new self();

        $self->name = $args['name'];
        $self->guard = $args['guard'] ?? GUARD::USER;

        foreach ($args['translations'] as $translation) {
            $self->translations[] = PermissionTranslationDto::byArgs($translation);
        }

        return $self;
    }

    public function getTranslations(): array
    {
        return $this->translations;
    }
}
