<?php

namespace App\GraphQL\Mutations\Common\Localization;

use App\Entities\Messages\ResponseMessageEntity;
use App\GraphQL\Types\Messages\ResponseMessageType;
use App\GraphQL\Types\NonNullType;
use App\Modules\Admin\Models\Admin;
use App\Modules\Localization\Contracts\Languageable;
use App\Modules\Localization\Rules\ExistsLanguages;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\User\Models\User;
use Closure;
use Core\GraphQL\Mutations\BaseMutation;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\SelectFields;

class SetLanguageMutation extends BaseMutation
{
    public const NAME = 'setLanguage';

    public function authorize($root, array $args, $ctx, ResolveInfo $info = null, Closure $fields = null): bool
    {
        return $this->authCheck(Guard::list());
    }

    public function args(): array
    {
        return [
            'lang' => NonNullType::string(),
        ];
    }

    public function type(): Type
    {
        return ResponseMessageType::type();
    }

    public function doResolve(
        $root,
        array $args,
        $context,
        ResolveInfo $info,
        SelectFields $fields
    ): ResponseMessageEntity
    {
        $lang = $args['lang'];
        try {
            $guard = null;
            if($this->authCheck(User::GUARD)){
                $guard = User::GUARD;
            }
            if($this->authCheck(Admin::GUARD)){
                $guard = Admin::GUARD;
            }

            /** @var $model Languageable */
            $model = $this->user($guard);
            $model->setLang($lang);

            return ResponseMessageEntity::success(__('messages.localization.success_set_lang'));
        } catch (\Throwable $e) {
            return ResponseMessageEntity::fail($e->getMessage());
        }
    }

    protected function rules(array $args = []): array
    {
        return [
            'lang' => ['required', 'string', 'min:2', 'max:3', new ExistsLanguages()],
        ];
    }
}
