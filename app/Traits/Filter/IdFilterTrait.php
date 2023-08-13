<?php

namespace App\Traits\Filter;

trait IdFilterTrait
{
    public function id(int $id): void
    {
        $this->where('id', $id);
    }

    public function ids(array $ids): void
    {
         $this->whereIn('id', $ids);
    }

    public function without(int $id): void
    {
        $this->whereNot('id', $id);
    }
}
