<?php

namespace Tests\Unit\Modules\Permissions\Actions\Role;

use App\Modules\Permissions\Actions\Role\RoleUpdateAction;
use App\Modules\Permissions\Dto\RoleEditDto;
use App\Modules\Permissions\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Lang;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\Builders\Permissions\RoleBuilder;
use Tests\TestCase;

class RoleUpdateActionTest extends TestCase
{
    use DatabaseTransactions;

    protected LanguageBuilder $langBuilder;
    protected PermissionBuilder $permissionBuilder;
    protected RoleBuilder $roleBuilder;

    protected array $data = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->langBuilder = resolve(LanguageBuilder::class);
        $this->permissionBuilder = resolve(PermissionBuilder::class);
        $this->roleBuilder = resolve(RoleBuilder::class);

        $this->data = [
            'name' => 'admin',
            'guard' => 'admin',
            'permissions' => [],
            'translations' => [],
        ];
    }

    /** @test */
    public function success_update()
    {
        $lang_1 = $this->langBuilder->active()->create();
        $lang_2 = $this->langBuilder->active()->create();
        Lang::setLocale($lang_1->slug);

        $perm_1 = $this->permissionBuilder->create();
        $perm_2 = $this->permissionBuilder->create();
        $perm_3 = $this->permissionBuilder->create();

        /** @var $model Role */
        $model = $this->roleBuilder
            ->withTranslation()
            ->permissions($perm_1, $perm_2)
            ->create();

        $data = $this->data;
        $data['translations'] = [
            $lang_1->slug => [
                'title' => 'role.update.' . $lang_1->slug,
                'lang' => $lang_1->slug
            ],
            $lang_2->slug => [
                'title' => 'role.update.' . $lang_2->slug,
                'lang' => $lang_2->slug
            ]
        ];
        $data['permissions'] = [$perm_1->id, $perm_3->id];
        $data['name'] = 'update_role';

        $this->assertNotEquals($model->name, $data['name']);
        $this->assertCount(2, $model->translations);
        foreach ($data['translations'] as $lang => $translation){
            $t = $model->translations->where('lang', $lang)->first();
            $this->assertNotEquals($t->title, $translation['title']);
        }
        $this->assertCount(2, $model->permissions);
        $this->assertTrue($model->permissions->contains('id', $perm_1->id));
        $this->assertTrue($model->permissions->contains('id', $perm_2->id));

        /** @var $handler RoleUpdateAction */
        $handler = resolve(RoleUpdateAction::class);
        $model = $handler->exec($model, RoleEditDto::byArgs($data));

        $this->assertTrue($model instanceof Role);

        $model->refresh();

        $this->assertEquals($model->name, $data['name']);
        $this->assertCount(2, $model->translations);
        foreach ($data['translations'] as $lang => $translation){
            $t = $model->translations->where('lang', $lang)->first();
            $this->assertEquals($t->title, $translation['title']);
        }
        $this->assertCount(2, $model->permissions);
        $this->assertTrue($model->permissions->contains('id', $perm_1->id));
        $this->assertTrue($model->permissions->contains('id', $perm_3->id));
    }

    /** @test */
    public function success_update_permissions_as_key()
    {
        $lang_1 = $this->langBuilder->active()->create();
        $lang_2 = $this->langBuilder->active()->create();
        Lang::setLocale($lang_1->slug);

        $perm_1 = $this->permissionBuilder->create();
        $perm_2 = $this->permissionBuilder->create();
        $perm_3 = $this->permissionBuilder->create();

        /** @var $model Role */
        $model = $this->roleBuilder
            ->withTranslation()
            ->permissions($perm_1, $perm_2)
            ->create();

        $data = $this->data;
        $data['translations'] = [
            $lang_1->slug => [
                'title' => 'role.update.' . $lang_1->slug,
                'lang' => $lang_1->slug
            ],
            $lang_2->slug => [
                'title' => 'role.update.' . $lang_2->slug,
                'lang' => $lang_2->slug
            ]
        ];
        $data['permissions'] = [$perm_1->name, $perm_3->name];
        $data['permissions_as_key'] = true;
        $data['name'] = 'update_role';

        $this->assertCount(2, $model->permissions);
        $this->assertTrue($model->permissions->contains('id', $perm_1->id));
        $this->assertTrue($model->permissions->contains('id', $perm_2->id));

        /** @var $handler RoleUpdateAction */
        $handler = resolve(RoleUpdateAction::class);
        $model = $handler->exec($model, RoleEditDto::byArgs($data));

        $model->refresh();

        $this->assertCount(2, $model->permissions);
        $this->assertTrue($model->permissions->contains('id', $perm_1->id));
        $this->assertTrue($model->permissions->contains('id', $perm_3->id));
    }

    /** @test */
    public function success_update_only_name()
    {
        $lang_1 = $this->langBuilder->slug('en')->active()->create();
        Lang::setLocale($lang_1->slug);

        /** @var $model Role */
        $model = $this->roleBuilder
            ->withTranslation()
            ->create();

        $data = $this->data;
        $data['translations'] = [
            $lang_1->slug => [
                'title' => 'role.update.' . $lang_1->slug,
                'lang' => $lang_1->slug
            ],
            'ru' => [
                'title' => 'role.update.ru',
                'lang' => 'ru'
            ]
        ];
        $data['name'] = 'update_role';

        $this->assertNotEquals($model->name, $data['name']);
        $this->assertCount(1, $model->translations);

        /** @var $handler RoleUpdateAction */
        $handler = resolve(RoleUpdateAction::class);
        $model = $handler->exec($model, RoleEditDto::byArgs($data));

        $model->refresh();

        $this->assertEquals($model->name, $data['name']);
        $this->assertCount(1, $model->translations);
    }

    /** @test */
    public function success_delete_permissions()
    {
        $lang_1 = $this->langBuilder->active()->create();
        Lang::setLocale($lang_1->slug);

        $perm_1 = $this->permissionBuilder->create();
        $perm_2 = $this->permissionBuilder->create();

        /** @var $model Role */
        $model = $this->roleBuilder
            ->permissions($perm_1, $perm_2)
            ->create();

        $data = $this->data;

        $this->assertCount(2, $model->permissions);

        /** @var $handler RoleUpdateAction */
        $handler = resolve(RoleUpdateAction::class);
        $model = $handler->exec($model, RoleEditDto::byArgs($data));

        $model->refresh();

        $this->assertEmpty($model->permissions);
    }
}
