<?php

namespace Tests\Builders\Localization;

use App\Modules\Localization\Models\Language;
use Tests\Builders\BaseBuilder;

class LanguageBuilder extends BaseBuilder
{
    function modelClass(): string
    {
        return Language::class;
    }

    public function default(): self
    {
        $this->data['default'] = true;
        return $this;
    }

    public function sort(int $value): self
    {
        $this->data['sort'] = $value;
        return $this;
    }

    public function active(bool $value = true): self
    {
        $this->data['active'] = $value;
        return $this;
    }

    public function slug(string $value): self
    {
        $this->data['slug'] = $value;
        return $this;
    }
}
