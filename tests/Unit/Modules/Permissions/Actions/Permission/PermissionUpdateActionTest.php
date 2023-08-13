<?php

namespace Tests\Unit\Modules\Permissions\Actions\Permission;

use App\Enums\CacheKeyEnum;
use App\Modules\Permissions\Actions\Permission\PermissionUpdateAction;
use App\Modules\Permissions\Dto\PermissionEditDto;
use App\Modules\Permissions\Models\Permission;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\TestCase;

class PermissionUpdateActionTest extends TestCase
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

        $this->data = [];
    }

    /** @test */
    public function success_update()
    {
        $lang_1 = $this->langBuilder->active()->create();
        $lang_2 = $this->langBuilder->active()->create();
        Lang::setLocale($lang_1->slug);

        /** @var $model Permission */
        $model = $this->permissionBuilder->withTranslation()->create();

        $data['translations'] = [
            $lang_1->slug => [
                'title' => 'permission.update.' . $lang_1->slug,
                'lang' => $lang_1->slug
            ],
            $lang_2->slug => [
                'title' => 'permission.update.' . $lang_2->slug,
                'lang' => $lang_2->slug
            ]
        ];

        $this->assertCount(2, $model->translations);
        foreach ($data['translations'] as $lang => $translation){
            $t = $model->translations->where('lang', $lang)->first();
            $this->assertNotEquals($t->title, $translation['title']);
        }

        /** @var $handler PermissionUpdateAction */
        $handler = resolve(PermissionUpdateAction::class);
        $model = $handler->exec($model, PermissionEditDto::byArgs($data));

        $this->assertTrue($model instanceof Permission);

        $model->refresh();

        $this->assertCount(2, $model->translations);
        foreach ($data['translations'] as $lang => $translation){
            $t = $model->translations->where('lang', $lang)->first();
            $this->assertEquals($t->title, $translation['title']);
        }
    }

    /** @test */
    public function success_add_new_langs()
    {
        $lang_1 = $this->langBuilder->slug('en')->active()->create();
        Lang::setLocale($lang_1->slug);

        /** @var $model Permission */
        $model = $this->permissionBuilder->withTranslation()->create();

        $lang_2 = $this->langBuilder->slug('uk')->active()->create();

        $data = $this->data;
        $data['translations'] = [
            $lang_1->slug => [
                'title' => 'permission.update.' . $lang_1->slug,
                'lang' => $lang_1->slug
            ],
            $lang_2->slug => [
                'title' => 'permission.update.' . $lang_2->slug,
                'lang' => $lang_2->slug
            ],
            'ru' => [
                'title' => 'permission.update.ru',
                'lang' => 'ru'
            ]
        ];

        $this->assertCount(1, $model->translations);
        $this->assertNull($model->translations->where('lang', $lang_2)->first());

        Cache::tags(CacheKeyEnum::LANGUAGE)->flush();

        /** @var $handler PermissionUpdateAction */
        $handler = resolve(PermissionUpdateAction::class);
        $model = $handler->exec($model, PermissionEditDto::byArgs($data));

        $model->refresh();

        $this->assertCount(2, $model->translations);
        $this->assertEquals(
            $model->translations->where('lang', $lang_2->slug)->first()->title,
            $data['translations'][$lang_2->slug]['title']
        );
    }
}
