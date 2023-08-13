<?php

namespace App\Modules\Permissions\Dto;

final class PermissionEditDto
{
    /** @var array<PermissionTranslationDto> */
    private array $translations = [];

    public static function byArgs(array $args): self
    {

        $self = new self();

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
