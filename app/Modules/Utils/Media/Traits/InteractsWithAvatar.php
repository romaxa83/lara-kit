<?php

namespace App\Modules\Utils\Media\Traits;

use Core\Exceptions\TranslatedException;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait InteractsWithAvatar
{
    public function hasAvatar(): bool
    {
        return $this->hasMedia('avatar');
    }

    public function getAvatarUrl(): string
    {
        if ($url = $this->avatar()?->getFullUrl('avatar')) {
            return $url;
        }

        throw new TranslatedException('Avatar does not exist');
    }

    public function avatar(): ?Media
    {
//        dd($this->getFirstMedia('avatar'));
        return $this->getFirstMedia('avatar');
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function uploadAvatar(UploadedFile $file): void
    {
        $this->addMedia($file)
            ->toMediaCollection('avatar');
    }

    public function deleteAvatar(): void
    {
        $this->clearMediaCollection('avatar');
    }
}
