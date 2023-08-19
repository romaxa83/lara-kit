<?php

namespace Core\GraphQL\Types\Media;

use Core\GraphQL\Types\BaseType;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaType extends BaseType
{
    public const NAME = 'MediaType';
    public const MODEL = Media::class;

    protected const ALWAYS = [
        'id',
        'model_type',
        'model_id',
        'uuid',
        'collection_name',
        'name',
        'file_name',
        'mime_type',
        'disk',
        'conversions_disk',
        'size',
        'manipulations',
        'custom_properties',
        'generated_conversions',
        'responsive_images',
        'order_column',
        'created_at',
        'updated_at',
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'always' => self::ALWAYS,
            ],
            'url' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => static fn(Media $m) => $m->getUrl(),
                'selectable' => false,
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'file_name' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'size' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'mime_type' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'conventions' => [
                /** @see MediaType::resolveConventionsField() */
                'type' => MediaConversionType::list(),
                'selectable' => false,
            ]
        ];
    }

    protected function resolveConventionsField(Media $m): Collection
    {
        $conversions = collect();

        foreach ($m->getGeneratedConversions() as $conversion => $isGenerated) {
            if ($isGenerated) {
                $conversions->push(
                    [
                        'convention' => $conversion,
                        'url' => $m->getUrl($conversion),
                    ]
                );
            }
        }

        return $conversions;
    }
}

