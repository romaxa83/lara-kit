<?php

namespace App\Modules\Utils\Phones\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FilterPhone
{
    public function phone(string $value): void
    {
        $this->whereHas('phones',fn (Builder $b) => $b
            ->whereRaw(sprintf("LOWER(%s) LIKE ?", 'phone'), ["$value%"])
        );
    }
}


