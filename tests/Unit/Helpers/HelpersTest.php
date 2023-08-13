<?php

namespace Tests\Unit\Helpers;

use App\Enums\CacheKeyEnum;
use App\Modules\Localization\Exceptions\LocalizationException;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    use DatabaseTransactions;
    protected LanguageBuilder $langBuilder;


    public function setUp(): void
    {
        parent::setUp();

        $this->langBuilder = resolve(LanguageBuilder::class);
    }

    /** @test */
    public function remove_underscore(): void
    {
        $this->assertEquals('test', remove_underscore('test'));
        $this->assertEquals('test ley', remove_underscore('test_ley'));
        $this->assertEquals('test ley test', remove_underscore('test_ley_test'));
    }

    /** @test */
    public function app_languages_empty(): void
    {
        $this->assertEmpty(app_languages());
    }

    /** @test */
    public function app_languages_exist(): void
    {
        Cache::tags(CacheKeyEnum::LANGUAGE)->flush();

        $lang_1 = $this->langBuilder->active()->create();
        $lang_2 = $this->langBuilder->active()->create();
        $lang_3 = $this->langBuilder->active(false)->create();

        $this->assertEmpty(Cache::tags(CacheKeyEnum::LANGUAGE)->get(CacheKeyEnum::LANGUAGE . '_slug_name'));
        $this->assertEmpty(Cache::tags(CacheKeyEnum::LANGUAGE)->get(CacheKeyEnum::LANGUAGE . '_locale_name'));

        $this->assertEquals(app_languages(),[
            $lang_1->slug => $lang_1->name,
            $lang_2->slug => $lang_2->name
        ]);

        $this->assertEquals(
            Cache::tags(CacheKeyEnum::LANGUAGE)->get(CacheKeyEnum::LANGUAGE . '_slug_name')
            , app_languages()
        );
        $this->assertEmpty(Cache::tags(CacheKeyEnum::LANGUAGE)->get(CacheKeyEnum::LANGUAGE . '_locale_name'));

        $this->assertEquals(app_languages('locale'),[
            $lang_1->locale => $lang_1->name,
            $lang_2->locale => $lang_2->name
        ]);

        $this->assertEquals(
            Cache::tags(CacheKeyEnum::LANGUAGE)->get(CacheKeyEnum::LANGUAGE . '_slug_name')
            , app_languages()
        );
        $this->assertEquals(
            Cache::tags(CacheKeyEnum::LANGUAGE)->get(CacheKeyEnum::LANGUAGE . '_locale_name'),
            app_languages('locale')
        );

        Cache::tags(CacheKeyEnum::LANGUAGE)->flush();
    }

    /** @test */
    public function is_support_lang(): void
    {
        Cache::tags(CacheKeyEnum::LANGUAGE)->flush();

        $this->langBuilder->slug('en')->active()->create();
        $this->langBuilder->slug('uk')->active(false)->create();

        $this->assertTrue(is_support_lang('en'));
        $this->assertFalse(is_support_lang('uk'));
        $this->assertFalse(is_support_lang('ff'));

        Cache::tags(CacheKeyEnum::LANGUAGE)->flush();
    }

    /** @test */
    public function support_langs_as_str(): void
    {
        $this->langBuilder->slug('en')->active()->create();
        $this->langBuilder->slug('uk')->active()->create();
        $this->langBuilder->slug('fr')->active(false)->create();

        $this->assertEquals('en, uk', support_langs_as_str());

    }

    /** @test */
    public function default_lang(): void
    {
        Cache::tags(CacheKeyEnum::LANGUAGE)->flush();

        $lang_1 = $this->langBuilder->active()->default()->create();
        $this->langBuilder->active()->create();
        $this->langBuilder->active(false)->create();

        $this->assertEmpty(Cache::tags(CacheKeyEnum::LANGUAGE)->get(CacheKeyEnum::DEFAULT_LANGUAGE));

        $this->assertEquals(default_lang()->id, $lang_1->id);

        $this->assertEquals(
            Cache::tags(CacheKeyEnum::LANGUAGE)->get(CacheKeyEnum::DEFAULT_LANGUAGE)
            , default_lang()
        );

        Cache::tags(CacheKeyEnum::LANGUAGE)->flush();
    }

    /** @test */
    public function default_lang_not_set(): void
    {
        Cache::tags(CacheKeyEnum::LANGUAGE)->flush();

        $this->expectException(LocalizationException::class);
        $this->expectExceptionMessage(__('exceptions.localization.default_language_not_set'));

        default_lang();

        Cache::tags(CacheKeyEnum::LANGUAGE)->flush();
    }

    /** @test */
    public function phone_clear(): void
    {
        $phone = '+38(095) 450-00-11';

        $this->assertEquals('380954500011', phone_clear($phone));
    }

    /** @test */
    public function is_contains(): void
    {
        $this->assertTrue(is_contains('380954500011'));
        $this->assertFalse(is_contains(''));
        $this->assertFalse(is_contains(null));

        $data['key'] = 'test';
        $this->assertTrue(is_contains($data, 'key'));

        $data['key'] = '';
        $this->assertFalse(is_contains($data, 'key'));

        $data['key'] = null;
        $this->assertFalse(is_contains($data, 'key'));

        $this->assertFalse(is_contains($data, 'not_key'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('function "is_contains" must have a key');
        is_contains($data, );
    }

    /** @test */
    public function hash(): void
    {
        $this->assertEquals('b45cffe084dd3d20d928bee85e7b0f21', hash_data('string'));
        $this->assertEquals('d0336e3b94fd76fc2ef669058d11917a', hash_data(56567));
        $this->assertEquals('a7353f7cddce808de0032747a0b7be50', hash_data(['key' => 'value']));
    }
}
