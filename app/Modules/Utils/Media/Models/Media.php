<?php

namespace App\Modules\Utils\Media\Models;

use Carbon\Carbon;
use Database\Factories\Utils\Media\MediaFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

/**
 * @property int $id
 * @property string $model_type
 * @property string $model_id
 * @property string|null $uuid
 * @property string $collection_name
 * @property string $name
 * @property string $file_name
 * @property string|null $mime_type
 * @property string $disk
 * @property string|null $conversions_disk
 * @property int $size
 * @property array $manipulations
 * @property array $custom_properties
 * @property array $generate_conversions
 * @property array $responsive_images
 * @property int $sort
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static MediaFactory factory(...$options)
 */
class Media extends SpatieMedia
{
    public const TABLE = 'media';

    protected $fillable = [
        'sort'
    ];

    protected static function newFactory(): Factory
    {
        return MediaFactory::new();
    }
}
