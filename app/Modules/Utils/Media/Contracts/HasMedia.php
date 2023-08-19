<?php

namespace App\Modules\Utils\Media\Contracts;


use App\Modules\Utils\Media\Models\Media;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

/**
 * @property-read MediaCollection|Media[] media
 */
interface HasMedia extends \Spatie\MediaLibrary\HasMedia
{
    public function getMediaCollectionName(): string;
}
