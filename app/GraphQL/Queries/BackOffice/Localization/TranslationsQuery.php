<?php

namespace App\GraphQL\Queries\BackOffice\Localization;

use App\GraphQL\Types\Localization\TranslateType;
use App\Modules\Localization\Models\Translation;
use App\Modules\Localization\Repositories\TranslationRepository;
use App\Permissions\Localization\Translation\TranslationListPermission;
use Core\GraphQL\Queries\BaseQuery;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Rebing\GraphQL\Support\SelectFields;

class TranslationsQuery extends BaseQuery
{
    public const NAME = 'translations';
    public const PERMISSION = TranslationListPermission::KEY;

    public function __construct(protected TranslationRepository $repo)
    {
        $this->setAdminGuard();
    }

    public function type(): Type
    {
        return TranslateType::paginate();
    }

    public function args(): array
    {
        return array_merge(
            $this->paginationArgs(),
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
        SelectFields $fields
    ): LengthAwarePaginator
    {
        return $this->repo->getTranslationsAsPaginator(
            $fields->getSelect() ?: ['id'],
            $args
        );
    }

    protected function rules(array $args = []): array
    {
        return [
            'place' => ['nullable', 'array'],
            'lang' => ['nullable', 'array'],
        ];
    }

    protected function allowedForSortFields(): array
    {
        return Translation::ALLOWED_SORTING_FIELDS;
    }
}
