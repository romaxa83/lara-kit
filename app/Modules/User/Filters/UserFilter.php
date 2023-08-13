<?php

namespace App\Modules\User\Filters;

use App\Modules\User\Models\User;
use App\Modules\Utils\Phones\Traits\FilterPhone;
use Core\Filters\BaseModelFilter;
use Illuminate\Database\Eloquent\Builder;

class UserFilter extends BaseModelFilter
{
    use FilterPhone;

    public function query(string $query): void
    {
        $this->where(fn (Builder $b) => $b
            ->orWhereRaw(sprintf("LOWER(%s) LIKE ?", User::TABLE . '.name'), ["%$query%"])
            ->orWhereRaw(sprintf("LOWER(%s) LIKE ?", User::TABLE . '.email'), ["%$query%"])
        );
    }

    protected function allowedOrders(): array
    {
        return User::ALLOWED_SORTING_FIELDS;
    }
}
