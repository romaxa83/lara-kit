<?php

namespace App\Modules\Utils\Media\Contracts;

use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

interface HasAvatar
{
    public function hasAvatar(): bool;

    public function getAvatarUrl(): string;

    public function avatar(): ?Media;

    public function uploadAvatar(UploadedFile $file): void;

    public function deleteAvatar(): void;
}
