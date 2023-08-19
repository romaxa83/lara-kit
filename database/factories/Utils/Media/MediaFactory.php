<?php

namespace Database\Factories\Utils\Media;

use App\Modules\Utils\Media\Models\Media;
use Database\Factories\BaseFactory;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Media|Media[]|Collection create(array $attrs = [])
 */
class MediaFactory extends BaseFactory
{
    protected $model = Media::class;

    public function definition(): array
    {
        return [
            'model_type' => $this->faker->word(),
            'model_id' => 1,
            'uuid' => $this->faker->uuid,
            'collection_name' => $this->faker->word,
            'name' => $this->faker->word,
            'file_name' => $this->faker->filePath(),
            'mime_type' => $this->faker->mimeType(),
            'disk' => 'public',
            'conversions_disk' => 'public',
            'size' => 22,
            'manipulations' => [],
            'custom_properties' => [],
            'generated_conversions' => [],
            'responsive_images' => [],
            'sort' => 2,
        ];
    }
}
