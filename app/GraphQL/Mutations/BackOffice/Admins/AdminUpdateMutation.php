<?php

namespace App\GraphQL\Mutations\BackOffice\Admins;

use App\GraphQL\InputTypes\Admins\AdminInput;
use App\GraphQL\Types\Admins\AdminType;
use App\GraphQL\Types\NonNullType;
use App\Modules\Admin\Actions\AdminUpdateAction;
use App\Modules\Admin\Dto\AdminDto;
use App\Modules\Admin\Models\Admin;
use App\Modules\Admin\Repositories\AdminRepository;
use App\Modules\Localization\Models\Language;
use App\Modules\Permissions\Models\Role;
use App\Modules\Utils\Phones\Rules\PhoneRule;
use App\Modules\Utils\Phones\Rules\PhoneUniqueRule;
use App\Permissions\Admins\AdminUpdatePermission;
use Core\GraphQL\Mutations\BaseMutation;
use Core\Rules\NameRule;
use Core\Rules\PasswordRule;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\SelectFields;

class AdminUpdateMutation extends BaseMutation
{
    public const NAME = 'adminUpdate';
    public const PERMISSION = AdminUpdatePermission::KEY;

    public function __construct(
        protected AdminRepository $repo,
        protected AdminUpdateAction $action
    )
    {
        $this->setAdminGuard();
    }

    public function type(): Type
    {
        return AdminType::type();
    }

    public function args(): array
    {
        return [
            'id' => NonNullType::id(),
            'input' => AdminInput::nonNullType(),
        ];
    }

    public function doResolve(
        $root,
        array $args,
        $context,
        ResolveInfo $info,
        SelectFields $fields
    ): Admin
    {
        /** @var $model Admin */
        $model = $this->repo->getBy('id', $args['id']);

        return $this->action->exec(
            $model,
            AdminDto::byArgs($args['input'])
        );
    }

    protected function rules(array $args = []): array
    {
        return [
            'id' => ['required', 'integer', Rule::exists(Admin::TABLE, 'id')],
            'input.name' => ['required', 'string', new NameRule()],
            'input.email' => ['required', 'string', 'email',
                Rule::unique(Admin::TABLE, 'email')->ignore($args['id'])
            ],
            'input.phone' => ['nullable', 'string', new PhoneRule(),
                new PhoneUniqueRule(Admin::class, ignoreValue: $args['id'])
            ],
            'input.password' => ['nullable', 'string', new PasswordRule()],
            'input.role' => ['required', 'int', Rule::exists(Role::TABLE, 'id')],
            'input.lang' => ['nullable', 'string', Rule::exists(Language::TABLE, 'slug')]
        ];
    }
}
