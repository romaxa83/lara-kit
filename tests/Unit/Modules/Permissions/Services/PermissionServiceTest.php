<?php

namespace Tests\Unit\Modules\Permissions\Services;

use App\Modules\Permissions\Enums\BaseRole;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Models\Role;
use App\Modules\Permissions\Services\PermissionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\TestCase;

class PermissionServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected PermissionService $service;
    protected LanguageBuilder $langBuilder;
    protected PermissionBuilder $permissionBuilder;

    protected array $data = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = resolve(PermissionService::class);

        $this->langBuilder = resolve(LanguageBuilder::class);
        $this->permissionBuilder = resolve(PermissionBuilder::class);

        $this->data = [];
    }

    /** @test */
    public function success_create_role_from_guard()
    {
        $guard = Guard::ADMIN();
        $role = BaseRole::SUPER_ADMIN;

        $lang_1 = $this->langBuilder->active()->create();
        $lang_2 = $this->langBuilder->active()->create();

        $perm_1 = $this->permissionBuilder->guard($guard)->create();
        $perm_2 = $this->permissionBuilder->guard($guard)->create();
        $perm_3 = $this->permissionBuilder->guard(Guard::USER())->create();

        $this->assertFalse(Role::query()->where('guard_name', $guard)->exists());


        $this->service->createBaseRole($role);

        $model = Role::query()->where('name', $role)->first();

        $this->assertEquals($model->name, $role);

        foreach (app_languages() as $lang => $name){
            $t = $model->translations->where('lang', $lang)->first();
            $this->assertEquals($t->title, ucfirst(remove_underscore($role)));
        }

        $this->assertCount(2, $model->permissions);
        $this->assertTrue($model->permissions->contains('id', $perm_1->id));
        $this->assertTrue($model->permissions->contains('id', $perm_2->id));
    }
}
