<?php

namespace App\GraphQL\Mutations\BackOffice\Admins;

use App\GraphQL\InputTypes\Admins\AdminInput;
use App\GraphQL\Types\Admins\AdminType;
use App\Modules\Admin\Actions\AdminCreateAction;
use App\Modules\Admin\Dto\AdminDto;
use App\Modules\Admin\Models\Admin;
use App\Modules\Localization\Models\Language;
use App\Modules\Permissions\Models\Role;
use App\Modules\Utils\Phones\Rules\PhoneRule;
use App\Modules\Utils\Phones\Rules\PhoneUniqueRule;
use App\Permissions\Admins\AdminCreatePermission;
use Core\GraphQL\Mutations\BaseMutation;
use Core\Rules\NameRule;
use Core\Rules\PasswordRule;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\SelectFields;

class AdminCreateMutation extends BaseMutation
{
    public const NAME = 'adminCreate';
    public const PERMISSION = AdminCreatePermission::KEY;

    public function __construct(protected AdminCreateAction $action)
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
        return $this->action->exec(
            AdminDto::byArgs($args['input'])
        );
    }

    protected function rules(array $args = []): array
    {
        return [
            'input.name' => ['required', 'string', new NameRule()],
            'input.email' => ['required', 'string', 'email', Rule::unique(Admin::class, 'email')],
            'input.password' => ['required', 'string', new PasswordRule()],
            'input.phone' => ['nullable', 'string', new PhoneRule(), new PhoneUniqueRule(Admin::class)],
            'input.role' => ['required', 'int', Rule::exists(Role::TABLE, 'id')],
            'input.lang' => ['nullable', 'string', Rule::exists(Language::TABLE, 'slug')]
        ];
    }
}
