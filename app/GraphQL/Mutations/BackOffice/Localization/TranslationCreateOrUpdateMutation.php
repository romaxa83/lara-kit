<?php

namespace App\GraphQL\Mutations\BackOffice\Localization;

use App\Entities\Messages\ResponseMessageEntity;
use App\GraphQL\InputTypes\Localization\TranslationInput;
use App\GraphQL\Types\Messages\ResponseMessageType;
use App\Modules\Localization\Actions\Translation\TranslationsCreateOrUpdateAction;
use App\Modules\Localization\Rules\ExistsLanguages;
use App\Permissions\Localization\Translation\TranslationUpdatePermission;
use Core\GraphQL\Mutations\BaseMutation;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\SelectFields;

class TranslationCreateOrUpdateMutation extends BaseMutation
{
    public const NAME = 'translationCreateOrUpdate';
    public const PERMISSION = TranslationUpdatePermission::KEY;

    public function __construct(protected TranslationsCreateOrUpdateAction $action)
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
            'input' => TranslationInput::nonNullList(),
        ];
    }

    public function doResolve(
        $root,
        array $args,
        $context,
        ResolveInfo $info,
        SelectFields $fields
    ): ResponseMessageEntity
    {
        try {
            if(!$this->action->exec($args['input'])){
                throw new \Exception(__('messages.localization.translation.actions.install.fail'));
            }

            return ResponseMessageEntity::success(__('messages.localization.translation.actions.install.success'));
        } catch (\Throwable $e) {
            return ResponseMessageEntity::fail($e->getMessage());
        }
    }

    protected function rules(array $args = []): array
    {
        return [
            'input.*.place' => ['required', 'string', 'min:3', 'max:50'],
            'input.*.key' => ['required', 'string', 'min:3', 'max:500'],
            'input.*.text' => ['required', 'string', 'min:2', 'max:1000'],
            'input.*.lang' => ['required', 'string', 'min:2', 'max:3', new ExistsLanguages()],
        ];
    }
}
