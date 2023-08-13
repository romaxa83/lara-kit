<?php

namespace App\Modules\Articles\Enums;

use Core\Enums\BaseEnum;

/**
 * @method static static DRAFT()
 * @method static static PUBLISHED()
 */
class ArticleStatus extends BaseEnum
{
    public const DRAFT      = 'draft';
    public const PUBLISHED  = 'published';

    public function isDraft(): bool
    {
        return $this->is(self::DRAFT());
    }

    public function isPublished(): bool
    {
        return $this->is(self::PUBLISHED());
    }
}
