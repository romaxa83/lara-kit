<?php

namespace Tests\Unit\Modules\Localization\Actions\Language;

use App\Enums\CacheKeyEnum;
use App\Modules\Localization\Actions\Language\LanguageToggleDefaultAction;
use App\Modules\Localization\Exceptions\LocalizationException;
use App\Modules\Localization\Models\Language;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\TestCase;

class LanguageToggleDefaultActionTest extends TestCase
{
    use DatabaseTransactions;

    protected LanguageBuilder $langBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->langBuilder = resolve(LanguageBuilder::class);
    }

    /** @test */
    public function success_toggle_to_false()
    {
        Cache::tags(CacheKeyEnum::LANGUAGE)->rememberForever('key',fn() => 'value');

        /** @var $model Language */
        $model = $this->langBuilder->default()->active()->create();
        $model_2 = $this->langBuilder->active()->create();

        $this->assertTrue($model->isDefault());
        $this->assertFalse($model_2->isDefault());

        $this->assertEquals(Cache::tags(CacheKeyEnum::LANGUAGE)->get('key'), 'value');

        /** @var $handler LanguageToggleDefaultAction */
        $handler = resolve(LanguageToggleDefaultAction::class);
        $handler->exec($model);

        $model->refresh();
        $model_2->refresh();

        $this->assertFalse($model->isDefault());
        $this->assertTrue($model_2->isDefault());

        $this->assertNull(Cache::tags(CacheKeyEnum::LANGUAGE)->get('key'));
    }

    /** @test */
    public function success_toggle_to_true()
    {
        Cache::tags(CacheKeyEnum::LANGUAGE)->rememberForever('key',fn() => 'value');

        /** @var $model Language */
        $model = $this->langBuilder->default()->active()->create();
        $model_2 = $this->langBuilder->active()->create();

        $this->assertTrue($model->isDefault());
        $this->assertFalse($model_2->isDefault());

        $this->assertEquals(Cache::tags(CacheKeyEnum::LANGUAGE)->get('key'), 'value');

        /** @var $handler LanguageToggleDefaultAction */
        $handler = resolve(LanguageToggleDefaultAction::class);
        $handler->exec($model_2);

        $model->refresh();
        $model_2->refresh();

        $this->assertFalse($model->isDefault());
        $this->assertTrue($model_2->isDefault());

        $this->assertNull(Cache::tags(CacheKeyEnum::LANGUAGE)->get('key'));
    }

    /** @test */
    public function fail_toggle_to_false_not_active_language()
    {
        /** @var $model Language */
        $model = $this->langBuilder->default()->active()->create();
        $model_2 = $this->langBuilder->active(false)->create();

        $this->assertTrue($model->isDefault());
        $this->assertFalse($model_2->isDefault());

        $this->expectException(LocalizationException::class);
        $this->expectExceptionMessage(__('exceptions.localization.can\'t_toggle_not_another_active_lang'));

        /** @var $handler LanguageToggleDefaultAction */
        $handler = resolve(LanguageToggleDefaultAction::class);
        $handler->exec($model);
    }
}
