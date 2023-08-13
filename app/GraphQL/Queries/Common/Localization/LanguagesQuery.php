<?php

namespace App\GraphQL\Queries\Common\Localization;

use App\GraphQL\Types\Localization\LanguageType;
use App\Modules\Localization\Models\Language;
use App\Modules\Localization\Repositories\LanguageRepository;
use Core\GraphQL\Queries\BaseQuery;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Collection;
use Rebing\GraphQL\Support\SelectFields;

class LanguagesQuery extends BaseQuery
{
    public const NAME = 'languages';

    public function __construct(protected LanguageRepository $repo)
    {}

    public function type(): Type
    {
        return LanguageType::list();
    }

    public function args(): array
    {
        return array_merge(
            $this->sortArgs(),
            [
                'active' => Type::boolean(),
            ]
        );
    }

    public function doResolve(
        $root,
        array $args,
        $context,
        ResolveInfo $info,
        SelectFields $fields
    ): Collection
    {
        return $this->repo->getLanguages(
            $fields->getSelect() ?: ['id'],
            $args
        );
    }

    protected function allowedForSortFields(): array
    {
        return Language::ALLOWED_SORTING_FIELDS;
    }
}
