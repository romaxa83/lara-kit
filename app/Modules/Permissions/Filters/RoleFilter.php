<?php

namespace App\Modules\Permissions\Filters;

use App\Modules\Permissions\Models\Role;
use Core\Traits\Filters\IdFilterTrait;
use Core\Traits\Filters\SortFilterTrait;
use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

class RoleFilter extends ModelFilter
{
    use SortFilterTrait;
    use IdFilterTrait;

    public function title(string $title): void
    {
        $title = strtolower($title);

        $this->whereHas('translation',
            fn (Builder $b): Builder => $b->where(
                fn (Builder $b): Builder => $b->orWhereRaw('LOWER(title) LIKE ?', ["%$title%"])
            )
        );
    }

    public function name(string $value): void
    {
        $value = strtolower($value);

        $this->whereRaw(sprintf("LOWER(%s) LIKE ?", 'name'), ["$value%"]);
    }

    public function guard(string $value): void
    {
        $value = strtolower($value);

        $this->where('guard_name', $value);
    }

    private function allowedOrders(): array
    {
        return Role::ALLOWED_SORTING_FIELDS;
    }

    private function allowedTranslateOrders(): array
    {
        return ['title'];
    }
}
