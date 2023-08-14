<?php

declare(strict_types=1);

namespace Core\Traits\Filters;

trait BetweenDateRangeFilterTrait
{
    public function dateFrom(string $date): void
    {
        $this->whereDate(self::$betweenDateColumnName ?? 'created_at', '>=', now()->parse($date));
    }

    public function dateTo(string $date): void
    {
        $this->whereDate(self::$betweenDateColumnName ?? 'created_at', '<=', now()->parse($date));
    }
}
