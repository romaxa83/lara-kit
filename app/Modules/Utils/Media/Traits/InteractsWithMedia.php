<?php

namespace App\Modules\Utils\Media\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

trait InteractsWithMedia
{
    use \Spatie\MediaLibrary\InteractsWithMedia;

    public function media(): MorphMany
    {
        return $this->morphMany(config('media-library.media_model'), 'model')->orderBy('sort');
    }

    public static function mimeArchive(): array
    {
        return [
            'application/octet-stream',
            'application/x-rar-compressed',
            'application/x-zip-compressed',
            'application/zip',
            'multipart/x-zip',
        ];
    }

    public function getMediaCollectionName(): string
    {
        return $this->resolveMediaCollectionName();
    }

    public function resolveMediaCollectionName(): string
    {
        if (defined(static::class . '::MEDIA_COLLECTION_NAME')) {
            return static::MEDIA_COLLECTION_NAME;
        }

        return 'default';
    }

    public function getMultiLangMediaCollectionName(string $lang): string
    {
        return $lang . '_' . $this->resolveMediaCollectionName();
    }

    /**
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        //original image in webp format:
        $this->addMediaConversion($this->getOriginalWebpConversionName())
            ->format(Manipulations::FORMAT_WEBP);

        if (defined(static::class . '::CONVERSIONS')) {
            foreach (static::CONVERSIONS ?? [] as $conversion => $size) {
                foreach (
                    [
                        $conversion => false,
                        $conversion . config('media-library.webp_conversion_suffix') => true
                    ] as $conversionName => $isWebp
                ) {
                    try {
                        $c = $this->addMediaConversion($conversionName);

                        if ($w = $size['width'] ?? null) {
                            $c->width($w);
                        }

                        if ($h = $size['height'] ?? null) {
                            $c->height($h);
                        }

                        if ($isWebp) {
                            $c->format(Manipulations::FORMAT_WEBP);
                        }
                    } catch (Throwable $e) {
                        logger($e);
                    }
                }
            }
        }
    }

    public function getOriginalWebpConversionName(): string
    {
        return config('media-library.original_webp');
    }

    protected function mimePdf(): array
    {
        return [
            'application/pdf',
        ];
    }

    protected function mimeWord(): array
    {
        return [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
    }

    protected function mimeExcel(): array
    {
        return [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            'application/vnd.ms-excel',
            'application/vnd.ms-excel.sheet.macroEnabled.12',
        ];
    }

    protected function mimeImage(): array
    {
        return [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/bmp',
            'image/gif',
            'image/svg+xml',
            'image/webp',
        ];
    }

    protected function mimeVideo(): array
    {
        return [
            'video/mp4',
            'video/webm',
            'video/webp',
        ];
    }
}
