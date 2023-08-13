<?php

namespace Tests\Unit\Modules\Localization\Actions\Language;

use App\Enums\CacheKeyEnum;
use App\Modules\Localization\Actions\Language\LanguageCreateAction;
use App\Modules\Localization\Dto\LanguageDto;
use App\Modules\Localization\Exceptions\LocalizationException;
use App\Modules\Localization\Models\Language;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\TestCase;

class LanguageCreateActionTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected LanguageBuilder $langBuilder;

    protected array $data = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->langBuilder = resolve(LanguageBuilder::class);

        $this->data = [
            'name' => $this->faker->country,
            'slug' => $this->faker->countryCode,
            'locale' => $this->faker->locale,
            'default' => true,
            'active' => true,
        ];
    }

    /** @test */
    public function success_create_all_fields()
    {
        Cache::tags(CacheKeyEnum::LANGUAGE)->rememberForever('key',fn() => 'value');

        $data = $this->data;

        $this->assertFalse(Language::query()->where('name', data_get($data, 'name'))->exists());

        $this->assertEquals(Cache::tags(CacheKeyEnum::LANGUAGE)->get('key'), 'value');

        /** @var $handler LanguageCreateAction */
        $handler = resolve(LanguageCreateAction::class);
        $model = $handler->exec(LanguageDto::byArgs($data));

        $this->assertTrue($model instanceof Language);

        $model = Language::query()->where('name', data_get($data, 'name'))->first();

        $this->assertEquals($model->slug, data_get($data, 'slug'));
        $this->assertEquals($model->locale, data_get($data, 'locale'));
        $this->assertEquals($model->default, data_get($data, 'default'));
        $this->assertEquals($model->active, data_get($data, 'active'));

        $this->assertNull(Cache::tags(CacheKeyEnum::LANGUAGE)->get('key'));
    }

    /** @test */
    public function success_create_required_fields()
    {
        $data = $this->data;
        unset(
            $data['default'],
            $data['active'],
        );

        $this->assertFalse(Language::query()->where('name', data_get($data, 'name'))->exists());

        /** @var $handler LanguageCreateAction */
        $handler = resolve(LanguageCreateAction::class);
        $model = $handler->exec(LanguageDto::byArgs($data));

        $this->assertTrue($model instanceof Language);

        $model = Language::query()->where('name', data_get($data, 'name'))->first();

        $this->assertEquals($model->slug, data_get($data, 'slug'));
        $this->assertEquals($model->locale, data_get($data, 'locale'));
        $this->assertTrue($model->isDefault());
        $this->assertTrue($model->isActive());
    }

    /** @test */
    public function fail_cant_create_another_default_model()
    {
        $this->langBuilder->default()->create();

        $data = $this->data;
        $data['default'] = true;

        $this->expectException(LocalizationException::class);
        $this->expectExceptionMessage(__('exceptions.localization.default_language_can_be_only_one'));

        /** @var $handler LanguageCreateAction */
        $handler = resolve(LanguageCreateAction::class);
        $handler->exec(LanguageDto::byArgs($data));
    }

    /** @test */
    public function fail_create_first_model_as_not_active()
    {
        $data = $this->data;
        $data['active'] = false;
        $data['default'] = true;

        $this->expectException(LocalizationException::class);
        $this->expectExceptionMessage(__('exceptions.localization.first_language_must_be_active_and_default'));

        /** @var $handler LanguageCreateAction */
        $handler = resolve(LanguageCreateAction::class);
        $handler->exec(LanguageDto::byArgs($data));
    }

    /** @test */
    public function fail_create_first_model_as_not_default()
    {
        $data = $this->data;
        $data['active'] = true;
        $data['default'] = false;

        $this->expectException(LocalizationException::class);
        $this->expectExceptionMessage(__('exceptions.localization.first_language_must_be_active_and_default'));

        /** @var $handler LanguageCreateAction */
        $handler = resolve(LanguageCreateAction::class);
        $handler->exec(LanguageDto::byArgs($data));
    }

    /** @test */
    public function fail_create_first_model_as_not_default_and_active()
    {
        $data = $this->data;
        $data['active'] = false;
        $data['default'] = false;

        $this->expectException(LocalizationException::class);
        $this->expectExceptionMessage(__('exceptions.localization.first_language_must_be_active_and_default'));

        /** @var $handler LanguageCreateAction */
        $handler = resolve(LanguageCreateAction::class);
        $handler->exec(LanguageDto::byArgs($data));
    }
}
