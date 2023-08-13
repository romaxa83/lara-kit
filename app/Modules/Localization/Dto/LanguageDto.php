<?php

namespace App\Modules\Localization\Dto;

final class LanguageDto
{
    public string $name;
    public string $slug;
    public string $locale;
    public bool $default;
    public bool $active;
    public int $sort;

    public static function byArgs(array $args): self
    {
        $self = new self();

        $self->name = data_get($args, 'name');
        $self->slug = data_get($args, 'slug');
        $self->locale = data_get($args, 'locale');
        $self->default = data_get($args, 'default', true);
        $self->active = data_get($args, 'active', true);
        $self->sort = data_get($args, 'sort', 0);

        return $self;
    }
}
