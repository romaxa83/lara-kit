<?php

namespace Tests\Unit\Modules\Permissions\Actions\Permission;

use App\Modules\Permissions\Actions\Permission\PermissionCreateAction;
use App\Modules\Permissions\Dto\PermissionDto;
use App\Modules\Permissions\Models\Permission;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Lang;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\TestCase;

class PermissionCreateActionTest extends TestCase
{
    use DatabaseTransactions;

    protected LanguageBuilder $langBuilder;

    protected array $data = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->langBuilder = resolve(LanguageBuilder::class);

        $this->data = [
            'name' => 'admin',
            'guard' => 'admin',
        ];
    }

    /** @test */
    public function success_create_all_fields()
    {
        $lang_1 = $this->langBuilder->active()->create();
        $lang_2 = $this->langBuilder->active()->create();
        Lang::setLocale($lang_1->slug);

        $data = $this->data;
        $data['translations'] = [
            $lang_1->slug => [
                'title' => 'permission.create.' . $lang_1->slug,
                'lang' => $lang_1->slug
            ],
            $lang_2->slug => [
                'title' => 'permission.create.' . $lang_2->slug,
                'lang' => $lang_2->slug
            ]
        ];

        $this->assertFalse(Permission::query()->where('name', data_get($data, 'name'))->exists());

        /** @var $handler PermissionCreateAction */
        $handler = resolve(PermissionCreateAction::class);
        $model = $handler->exec(PermissionDto::byArgs($data));

        $this->assertTrue($model instanceof Permission);

        $model = Permission::query()->where('name', data_get($data, 'name'))->first();

        $this->assertEquals($model->guard_name, data_get($data, 'guard'));

        $this->assertEquals($model->translation->lang, Lang::getLocale());
        $this->assertEquals($model->translation->title, $data['translations'][Lang::getLocale()]['title']);

        foreach ($data['translations'] as $lang => $translation){
            $t = $model->translations->where('lang', $lang)->first();
            $this->assertEquals($t->title, $translation['title']);
        }
    }

    /** @test */
    public function success_create_only_support_languages()
    {
        $lang_1 = $this->langBuilder->slug('en')->active()->create();
        $lang_2 = $this->langBuilder->slug('uk')->active(false)->create();
        Lang::setLocale($lang_1->slug);

        $data = $this->data;
        $data['translations'] = [
            $lang_1->slug => [
                'title' => 'permission.create.' . $lang_1->slug,
                'lang' => $lang_1->slug
            ],
            $lang_2->slug => [
                'title' => 'permission.create.' . $lang_2->slug,
                'lang' => $lang_2->slug
            ],
            'ru' => [
                'title' => 'permission.create.ru',
                'lang' => 'ru'
            ]
        ];

        /** @var $handler PermissionCreateAction */
        $handler = resolve(PermissionCreateAction::class);
        $model = $handler->exec(PermissionDto::byArgs($data));

        $this->assertCount(1, $model->translations);
        $this->assertEquals($lang_1->slug, $model->translations[0]->lang);
    }
}
