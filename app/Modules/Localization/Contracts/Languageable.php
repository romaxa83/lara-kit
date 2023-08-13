<?php

namespace App\Modules\Localization\Contracts;

interface Languageable
{
    public function getLangSlug(): ?string;

    public function setLang(string $lang): void;
}
