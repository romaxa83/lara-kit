<?php

namespace App\GraphQL\Mutations\BackOffice\Localization;

use App\Entities\Messages\ResponseMessageEntity;
use App\GraphQL\InputTypes\Localization\TranslationDeleteInput;
use App\GraphQL\Types\Messages\ResponseMessageType;
use App\Modules\Localization\Actions\Translation\TranslationsDeleteAction;
use App\Modules\Localization\Rules\ExistsLanguages;
use App\Permissions\Localization\Translation\TranslationDeletePermission;
use Core\GraphQL\Mutations\BaseMutation;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\SelectFields;

class TranslationDeleteMutation extends BaseMutation
{
    public const NAME = 'translationDelete';
    public const PERMISSION = TranslationDeletePermission::KEY;

    public function __construct(protected TranslationsDeleteAction $action)
    {
        $this->setAdminGuard();
    }

    public function type(): Type
    {
        return ResponseMessageType::type();
    }

    public function args(): array
    {
        return [
            'input' => TranslationDeleteInput::nonNullList(),
        ];
    }

    /**
     * @throws Exception
     */
    public function doResolve(
        $root,
        array $args,
        $context,
        ResolveInfo $info,
        SelectFields $fields
    ): ResponseMessageEntity
    {
        try {
            $this->action->exec($args['input']);

            return ResponseMessageEntity::success(__('messages.localization.translation.actions.delete.success'));
        } catch (\Throwable $e) {
            return ResponseMessageEntity::fail($e->getMessage());
        }
    }

    protected function rules(array $args = []): array
    {
        return [
            'input.*.place' => ['required', 'string'],
            'input.*.key' => ['required', 'string'],
            'input.*.lang' => ['required', 'string', new ExistsLanguages()],
        ];
    }
}
