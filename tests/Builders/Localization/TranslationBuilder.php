<?php

namespace Tests\Builders\Localization;

use App\Modules\Localization\Models\Translation;
use Tests\Builders\BaseBuilder;

class TranslationBuilder extends BaseBuilder
{
    function modelClass(): string
    {
        return Translation::class;
    }

    public function place(string $value): self
    {
        $this->data['place'] = $value;
        return $this;
    }

    public function key(string $value): self
    {
        $this->data['key'] = $value;
        return $this;
    }

    public function text(string $value): self
    {
        $this->data['text'] = $value;
        return $this;
    }

    public function lang(string $value): self
    {
        $this->data['lang'] = $value;
        return $this;
    }
}
