<?php

namespace App\Dto\Locales;

class LocaleDto
{
    private ?string $slug;
    private string $name;
    private string $native;
    private ?string $sort;

    public static function byArgs(array $args): self
    {
        $self = new self();

        $self->slug = data_get($args, 'slug');
        $self->name = $args['name'];
        $self->native = $args['native'];
        $self->sort = data_get($args, 'sort', 0);

        return $self;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNative(): string
    {
        return $this->native;
    }

    public function getSort(): string
    {
        return $this->sort;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
