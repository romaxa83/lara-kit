<?php

namespace App\GraphQL\Queries\Common\Localization;

use App\GraphQL\Types\Localization\TranslateType;
use App\Modules\Localization\Models\Translation;
use App\Modules\Localization\Repositories\TranslationRepository;
use Core\GraphQL\Queries\BaseQuery;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\SelectFields;

class TranslationsQuery extends BaseQuery
{
    public const NAME = 'translationsList';

    public function __construct(protected TranslationRepository $repo)
    {}

    public function type(): Type
    {
        return TranslateType::list();
    }

    public function args(): array
    {
        return array_merge(
            $this->sortArgs(),
            [
                'place' => Type::listOf(Type::string()),
                'key' => Type::string(),
                'text' => Type::string(),
                'lang' => Type::listOf(Type::string()),
            ]
        );
    }

    public function doResolve(
        $root,
        array $args,
        $context,
        ResolveInfo $info,
        SelectFields $fields)
    : array
    {
        return $this->repo->getTranslationsAsArray(
            $fields->getSelect() ?: ['id'],
            $args
        );
    }

    protected function rules(array $args = []): array
    {
        return [
            'place' => ['nullable', 'array'],
            'key' => ['nullable', 'string'],
            'lang' => ['nullable', 'array'],
            'text' => ['nullable', 'string'],
        ];
    }

    protected function allowedForSortFields(): array
    {
        return Translation::ALLOWED_SORTING_FIELDS;
    }
}
