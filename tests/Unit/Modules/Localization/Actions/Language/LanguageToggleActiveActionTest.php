<?php

namespace Tests\Unit\Modules\Localization\Actions\Language;

use App\Enums\CacheKeyEnum;
use App\Modules\Localization\Actions\Language\LanguageToggleActiveAction;
use App\Modules\Localization\Exceptions\LocalizationException;
use App\Modules\Localization\Models\Language;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\TestCase;

class LanguageToggleActiveActionTest extends TestCase
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

        $this->assertTrue($model->isActive());
        $this->assertTrue($model->isDefault());
        $this->assertTrue($model_2->isActive());

        $this->assertEquals(Cache::tags(CacheKeyEnum::LANGUAGE)->get('key'), 'value');

        /** @var $handler LanguageToggleActiveAction */
        $handler = resolve(LanguageToggleActiveAction::class);
        $handler->exec($model_2);

        $model->refresh();
        $model_2->refresh();

        $this->assertTrue($model->isActive());
        $this->assertTrue($model->isDefault());
        $this->assertFalse($model_2->isActive());

        $this->assertNull(Cache::tags(CacheKeyEnum::LANGUAGE)->get('key'));
    }

    /** @test */
    public function success_toggle_to_true()
    {
        /** @var $model Language */
        $model = $this->langBuilder->default()->active()->create();
        $model_2 = $this->langBuilder->active(false)->create();

        $this->assertTrue($model->isActive());
        $this->assertTrue($model->isDefault());
        $this->assertFalse($model_2->isActive());

        /** @var $handler LanguageToggleActiveAction */
        $handler = resolve(LanguageToggleActiveAction::class);
        $handler->exec($model_2);

        $model->refresh();
        $model_2->refresh();

        $this->assertTrue($model->isActive());
        $this->assertTrue($model->isDefault());
        $this->assertTrue($model_2->isActive());
    }

    /** @test */
    public function fail_toggle_to_false_as_default_model()
    {
        /** @var $model Language */
        $model = $this->langBuilder->default()->active()->create();

        $this->assertTrue($model->isActive());
        $this->assertTrue($model->isDefault());

        $this->expectException(LocalizationException::class);
        $this->expectExceptionMessage(__('exceptions.localization.can\'t_disable_default_language'));

        /** @var $handler LanguageToggleActiveAction */
        $handler = resolve(LanguageToggleActiveAction::class);
        $handler->exec($model);
    }
}

