<?php

namespace App\Modules\Admin\Filters;

use App\Modules\Admin\Models\Admin;
use App\Modules\Utils\Phones\Traits\FilterPhone;
use Core\Filters\BaseModelFilter;
use Illuminate\Database\Eloquent\Builder;

class AdminFilter extends BaseModelFilter
{
    use FilterPhone;

    public function query(string $query): void
    {
        $this->where(fn (Builder $b) => $b
            ->orWhereRaw(sprintf("LOWER(%s) LIKE ?", Admin::TABLE . '.name'), ["%$query%"])
            ->orWhereRaw(sprintf("LOWER(%s) LIKE ?", Admin::TABLE . '.email'), ["%$query%"])
        );
    }

    protected function allowedOrders(): array
    {
        return Admin::ALLOWED_SORTING_FIELDS;
    }
}
