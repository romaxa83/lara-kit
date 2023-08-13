<?php

namespace App\Modules\Localization\Dto;

final class LanguageEditDto
{
    public string $name;
    public string $slug;
    public string $locale;
    public bool $default;
    public bool $active;

    public static function byArgs(array $args): self
    {
        $self = new self();

        $self->default = data_get($args, 'default');
        $self->active = data_get($args, 'active');

        return $self;
    }
}
