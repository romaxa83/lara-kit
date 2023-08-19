<?php

namespace App\GraphQL\Types\Media;

use App\Modules\Utils\Media\Models\Media;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Collection;

class MediaType extends \Core\GraphQL\Types\Media\MediaType
{
    public const MODEL = Media::class;

    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                'url_webp' => [
                    /**
                     * @see MediaType::resolveUrlWebpField()
                     */
                    'type' => Type::string(),
                    'selectable' => false,
                ],
            ]
        );
    }

    protected function resolveUrlWebpField(\Spatie\MediaLibrary\MediaCollections\Models\Media $m): ?string
    {
        $webp = config('media-library.original_webp');

        if ($m->hasGeneratedConversion($webp)) {
            return $m->getUrl($webp);
        }

        return null;
    }

    protected function resolveConventionsField(\Spatie\MediaLibrary\MediaCollections\Models\Media $m): Collection
    {
        $conversions = collect();

        foreach ($m->getGeneratedConversions() as $conversion => $isGenerated) {
            $suffix = config('media-library.webp_conversion_suffix');

            $webp = $conversion . $suffix;

            if (str_contains($conversion, $suffix)) {
                continue;
            }

            if ($isGenerated) {
                $conversions->push(
                    [
                        'convention' => $conversion,
                        'url' => $m->getUrl($conversion),
                        'url_webp' => $m->hasGeneratedConversion($webp) ? $m->getUrl($webp) : null,
                    ]
                );
            }
        }

        return $conversions;
    }
}

