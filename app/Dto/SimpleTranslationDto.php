<?php

namespace App\Dto;

class SimpleTranslationDto
{
    public string $title;

    public string $lang;


    public static function byArgs(array $args): self
    {
        $self = new self();

        $self->title = $args['title'];
        $self->lang = $args['lang'];

        return $self;
    }
}

