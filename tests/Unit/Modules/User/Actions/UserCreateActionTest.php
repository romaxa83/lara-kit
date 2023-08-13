<?php

namespace Tests\Unit\Modules\User\Actions;

use App\Modules\Permissions\Enums\BaseRole;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Models\Role;
use App\Modules\User\Actions\UserCreateAction;
use App\Modules\User\Dto\UserDto;
use App\Modules\User\Models\User;
use App\Modules\Utils\Phones\Models\Phone;
use App\Modules\Utils\Phones\ValueObject\Phone as PhoneObj;
use DomainException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\Builders\Permissions\RoleBuilder;
use Tests\TestCase;

class UserCreateActionTest extends TestCase
{
    use DatabaseTransactions;

    protected LanguageBuilder $langBuilder;
    protected RoleBuilder $roleBuilder;
    protected PermissionBuilder $permissionBuilder;

    protected array $data = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->langBuilder = resolve(LanguageBuilder::class);
        $this->roleBuilder = resolve(RoleBuilder::class);
        $this->permissionBuilder = resolve(PermissionBuilder::class);

        $this->data = [
            'name' => 'admin',
            'email' => 'test@test.com',
            'password' => 'password',
            'phone' => '+38(095)451-49-49',
        ];

        $this->langInit();
    }

    /** @test */
    public function success_create()
    {
        /** @var $role Role */
        $role = $this->roleBuilder->asUser()->create();

        $lang_1 = $this->langBuilder->active()->create();

        $data = $this->data;
        $data['lang'] = $lang_1->slug;

        $this->assertNull(User::query()->where('email', $data['email'])->first());

        /** @var $handler UserCreateAction */
        $handler = resolve(UserCreateAction::class);
        $model = $handler->exec(UserDto::byArgs($data));

        $this->assertTrue($model instanceof User);

        $this->assertEquals($model->name, $data['name']);
        $this->assertEquals($model->email, $data['email']);
        $this->assertTrue(password_verify($data['password'], $model->password));
        $this->assertEquals($model->lang, $data['lang']);

        $this->assertCount(1, $model->phones);
        $this->assertEquals(
            $model->phone->phone,
            phone_clear($data['phone'])
        );
        $this->assertFalse($model->isPhoneVerified());

        $this->assertCount(1, $model->roles);
        $this->assertEquals($model->role->id, $role->id);
    }

    /** @test */
    public function success_create_verified_phone()
    {
        /** @var $role Role */
        $this->roleBuilder->asUser()->create();

        $data = $this->data;

        /** @var $handler UserCreateAction */
        $handler = resolve(UserCreateAction::class);
        $model = $handler->exec(UserDto::byArgs($data), verifyPhone: true);

        $this->assertCount(1, $model->phones);
        $this->assertTrue($model->phone instanceof Phone);
        $this->assertTrue($model->phone->phone instanceof PhoneObj);
        $this->assertEquals(
            $model->phone->phone,
            phone_clear($data['phone'])
        );

        $this->assertTrue($model->isPhoneVerified());
    }

    /** @test */
    public function success_create_without_phone()
    {
        /** @var $role Role */
        $this->roleBuilder->asUser()->create();

        $data = $this->data;
        unset($data['phone']);

        /** @var $handler UserCreateAction */
        $handler = resolve(UserCreateAction::class);
        $model = $handler->exec(UserDto::byArgs($data));

        $this->assertEquals($model->lang, default_lang()->slug);

        $this->assertCount(0, $model->phones);
        $this->assertNull($model->phone);
    }

    /** @test */
    public function fail_not_user_role()
    {
        $data = $this->data;

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(__('exceptions.role.not_found_user_role'));

        /** @var $handler UserCreateAction */
        $handler = resolve(UserCreateAction::class);
        $handler->exec(UserDto::byArgs($data));
    }
}
