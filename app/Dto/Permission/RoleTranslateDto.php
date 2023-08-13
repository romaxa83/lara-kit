<?php

namespace App\Dto\Permission;

class RoleTranslateDto
{
    private string $title;

    private string $language;


    public static function fromArgs(array $args): self
    {
        $self = new self();

        $self->title = $args['title'];
        $self->language = $args['language'];

        return $self;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }
}
