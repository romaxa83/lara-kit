<?php

namespace Tests\Unit\Modules\Permissions\Actions\Role;

use App\Modules\Permissions\Actions\Role\RoleCreateAction;
use App\Modules\Permissions\Dto\RoleDto;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Lang;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\TestCase;

class RoleCreateActionTest extends TestCase
{
    use DatabaseTransactions;

    protected LanguageBuilder $langBuilder;
    protected PermissionBuilder $permissionBuilder;

    protected array $data = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->langBuilder = resolve(LanguageBuilder::class);
        $this->permissionBuilder = resolve(PermissionBuilder::class);

        $this->data = [
            'name' => 'admin',
            'guard' => Guard::ADMIN,
            'permissions' => [],
            'translations' => [],
        ];
    }

    /** @test */
    public function success_create_all_fields()
    {
        $lang_1 = $this->langBuilder->active()->create();
        $lang_2 = $this->langBuilder->active()->create();
        Lang::setLocale($lang_1->slug);

        $perm_1 = $this->permissionBuilder->create();
        $perm_2 = $this->permissionBuilder->create();

        $data = $this->data;
        $data['translations'] = [
            $lang_1->slug => [
                'title' => 'role.create.' . $lang_1->slug,
                'lang' => $lang_1->slug
            ],
            $lang_2->slug => [
                'title' => 'role.create.' . $lang_2->slug,
                'lang' => $lang_2->slug
            ]
        ];
        $data['permissions'] = [$perm_1->id, $perm_2->id];

        $this->assertFalse(Role::query()->where('name', data_get($data, 'name'))->exists());

        /** @var $handler RoleCreateAction */
        $handler = resolve(RoleCreateAction::class);
        $model = $handler->exec(RoleDto::byArgs($data));

        $this->assertTrue($model instanceof Role);

        $model = Role::query()->where('name', data_get($data, 'name'))->first();

        $this->assertEquals($model->guard_name, data_get($data, 'guard'));

        $this->assertEquals($model->translation->lang, Lang::getLocale());
        $this->assertEquals($model->translation->title, $data['translations'][Lang::getLocale()]['title']);

        foreach ($data['translations'] as $lang => $translation){
            $t = $model->translations->where('lang', $lang)->first();
            $this->assertEquals($t->title, $translation['title']);
        }

        $this->assertCount(2, $model->permissions);
        $this->assertTrue($model->permissions->contains('id', $perm_1->id));
        $this->assertTrue($model->permissions->contains('id', $perm_2->id));
    }

    /** @test */
    public function success_create_permissions_as_key()
    {
        $lang_1 = $this->langBuilder->active()->create();
        $lang_2 = $this->langBuilder->active()->create();
        Lang::setLocale($lang_1->slug);

        $perm_1 = $this->permissionBuilder->create();
        $perm_2 = $this->permissionBuilder->create();

        $data = $this->data;
        $data['translations'] = [
            $lang_1->slug => [
                'title' => 'role.create.' . $lang_1->slug,
                'lang' => $lang_1->slug
            ],
            $lang_2->slug => [
                'title' => 'role.create.' . $lang_2->slug,
                'lang' => $lang_2->slug
            ]
        ];
        $data['permissions'] = [$perm_1->name, $perm_2->name];
        $data['permissions_as_key'] = true;

        $this->assertFalse(Role::query()->where('name', data_get($data, 'name'))->exists());

        /** @var $handler RoleCreateAction */
        $handler = resolve(RoleCreateAction::class);
        $model = $handler->exec(RoleDto::byArgs($data));

        $this->assertCount(2, $model->permissions);
        $this->assertTrue($model->permissions->contains('id', $perm_1->id));
        $this->assertTrue($model->permissions->contains('id', $perm_2->id));
    }

    /** @test */
    public function success_create_without_translation_and_perms()
    {
        $data = $this->data;

        $this->assertFalse(Role::query()->where('name', data_get($data, 'name'))->exists());

        /** @var $handler RoleCreateAction */
        $handler = resolve(RoleCreateAction::class);
        $model = $handler->exec(RoleDto::byArgs($data));

        $this->assertEquals($model->name, data_get($data, 'name'));
        $this->assertEquals($model->guard_name, data_get($data, 'guard'));

        $this->assertEmpty($model->translations);
        $this->assertEmpty($model->permissions);
    }
}
