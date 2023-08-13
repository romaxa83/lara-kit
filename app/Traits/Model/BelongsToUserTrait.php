<?php

declare(strict_types=1);

namespace App\Traits\Model;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @see BelongsToUserTrait::user()
 * @property-read User $user
 */
trait BelongsToUserTrait
{
    public function user(): BelongsTo|User
    {
        return $this->belongsTo(User::class);
    }
}
