<?php

namespace Tests\Unit\Modules\Permissions\Actions\Role;

use App\Modules\Permissions\Actions\Role\RoleDeleteAction;
use App\Modules\Permissions\Enums\BaseRole;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Exceptions\PermissionsException;
use App\Modules\Permissions\Models\Permission;
use App\Modules\Permissions\Models\Role;
use App\Modules\Permissions\Models\RoleTranslation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\Builders\Permissions\RoleBuilder;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;

class RoleDeleteActionTest extends TestCase
{
    use DatabaseTransactions;

    protected PermissionBuilder $permissionBuilder;
    protected RoleBuilder $roleBuilder;
    protected AdminBuilder $adminBuilder;
    protected UserBuilder $userBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->langBuilder = resolve(LanguageBuilder::class);
        $this->permissionBuilder = resolve(PermissionBuilder::class);
        $this->roleBuilder = resolve(RoleBuilder::class);
        $this->adminBuilder = resolve(AdminBuilder::class);
        $this->userBuilder = resolve(UserBuilder::class);

        $this->langInit();

    }

    /** @test */
    public function success_delete_as_model()
    {
        $perm_1 = $this->permissionBuilder->create();

        /** @var $model Role */
        $model = $this->roleBuilder
            ->withTranslation()
            ->permissions($perm_1)
            ->create();

        $modelId = $model->id;
        $translationId = $model->translation->id;

        /** @var $handler RoleDeleteAction */
        $handler = resolve(RoleDeleteAction::class);

        $this->assertTrue($handler->exec($model));

        $this->assertFalse(Role::query()->where('id', $modelId)->exists());
        $this->assertFalse(RoleTranslation::query()->where('id', $translationId)->exists());

        $this->assertTrue(Permission::query()->where('id', $perm_1->id)->exists());
    }

    /** @test */
    public function success_delete_as_id()
    {
        $perm_1 = $this->permissionBuilder->create();

        /** @var $model Role */
        $model = $this->roleBuilder
            ->withTranslation()
            ->permissions($perm_1)
            ->create();

        $modelId = $model->id;

        /** @var $handler RoleDeleteAction */
        $handler = resolve(RoleDeleteAction::class);

        $this->assertTrue($handler->exec($model->id));

        $this->assertFalse(Role::query()->where('id', $modelId)->exists());
    }

    /** @test */
    public function success_delete_as_ids()
    {
        $perm_1 = $this->permissionBuilder->create();

        /** @var $model Role */
        $model = $this->roleBuilder
            ->withTranslation()
            ->permissions($perm_1)
            ->create();
        $model_2 = $this->roleBuilder
            ->withTranslation()
            ->permissions($perm_1)
            ->create();

        $modelId = $model->id;
        $modelId_2 = $model_2->id;

        /** @var $handler RoleDeleteAction */
        $handler = resolve(RoleDeleteAction::class);

        $this->assertTrue($handler->exec([$model->id, $model_2->id]));

        $this->assertFalse(Role::query()->where('id', $modelId)->exists());
        $this->assertFalse(Role::query()->where('id', $modelId_2)->exists());
    }

    /** @test */
    public function success_delete_as_collection()
    {
        $perm_1 = $this->permissionBuilder->create();

        /** @var $model Role */
        $model = $this->roleBuilder
            ->withTranslation()
            ->permissions($perm_1)
            ->create();
        $model_2 = $this->roleBuilder
            ->withTranslation()
            ->permissions($perm_1)
            ->create();

        $modelId = $model->id;
        $modelId_2 = $model_2->id;

        $collections = Role::query()->whereIn('id', [$modelId, $modelId_2])->get();

        /** @var $handler RoleDeleteAction */
        $handler = resolve(RoleDeleteAction::class);

        $this->assertTrue($handler->exec($collections));

        $this->assertFalse(Role::query()->where('id', $modelId)->exists());
        $this->assertFalse(Role::query()->where('id', $modelId_2)->exists());
    }

    /** @test */
    public function fail_delete_as_model_attached_role_to_user()
    {
        /** @var $model Role */
        $model = $this->roleBuilder
            ->name(BaseRole::USER)
            ->guard(Guard::USER)
            ->create();

        $this->userBuilder->create();

        $this->expectException(PermissionsException::class);
        $this->expectExceptionMessage(__('exceptions.role.not_cant_delete_role'));

        /** @var $handler RoleDeleteAction */
        $handler = resolve(RoleDeleteAction::class);
        $handler->exec($model);
    }

    /** @test */
    public function fail_delete_as_model_attached_role_to_admin()
    {
        /** @var $model Role */
        $model = $this->roleBuilder
            ->create();

        $this->adminBuilder->role($model)->create();

        $this->expectException(PermissionsException::class);
        $this->expectExceptionMessage(__('exceptions.role.not_cant_delete_role'));

        /** @var $handler RoleDeleteAction */
        $handler = resolve(RoleDeleteAction::class);
        $handler->exec($model);
    }

    /** @test */
    public function fail_delete_as_array_attached_role_to_admin()
    {
        /** @var $model Role */
        $model = $this->roleBuilder->create();
        $model_2 = $this->roleBuilder->create();

        $this->adminBuilder->role($model_2)->create();

        $this->expectException(PermissionsException::class);
        $this->expectExceptionMessage(__('exceptions.role.not_cant_delete_role'));

        /** @var $handler RoleDeleteAction */
        $handler = resolve(RoleDeleteAction::class);
        $handler->exec([$model->id, $model_2->id]);
    }
}
