<?php

namespace App\Filters\Security;

use App\Models\Security\IpAccess;
use Core\Filters\BaseModelFilter;
use Core\Traits\Filters\SortFilterTrait;

/**
 * @mixin IpAccess
 */
class IpAccessFilter extends BaseModelFilter
{
    use SortFilterTrait;

    public function query(string $value): void
    {
        $this->where('address', 'like', "%$value%")
            ->orWhereRaw('LOWER(description) LIKE ?', ["%$value%"]);
    }

    public function customAddressSort(string $field, string $direction): void
    {
        $this->orderByRaw(sprintf('%s::inet %s', $field, $direction));
    }

    public function customActiveSort(string $field, string $direction): void
    {
        $this->orderBy($field, $direction)
            ->orderBy('id');
    }

    protected function allowedOrders(): array
    {
        return IpAccess::ALLOWED_SORTING_FIELDS;
    }
}
