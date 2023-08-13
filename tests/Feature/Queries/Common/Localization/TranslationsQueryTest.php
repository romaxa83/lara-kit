<?php

namespace Tests\Feature\Queries\Common\Localization;

use App\GraphQL\Queries\BackOffice;
use App\GraphQL\Queries\Common\Localization\TranslationsQuery;
use App\Modules\Localization\Models\Translation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Localization\TranslationBuilder;
use Tests\TestCase;

class TranslationsQueryTest extends TestCase
{
    use DatabaseTransactions;

    public const QUERY = TranslationsQuery::NAME;

    protected LanguageBuilder $languageBuilder;
    protected TranslationBuilder $translationBuilder;

    public function setUp(): void
    {
        parent::setUp();
        $this->languageBuilder = resolve(LanguageBuilder::class);
        $this->translationBuilder = resolve(TranslationBuilder::class);

        $this->langInit();
    }

    /** @test */
    public function success_get(): void
    {
//        $this->assertNull(
//            Cache::get(CacheKeyEnum::TRANSLATIONS .'_'. hash_data(array_merge(['key', 'place', 'text', 'lang'], [])))
//        );

        /** @var $model_1 Translation */
        $model_1 = $this->translationBuilder->create();
        $model_2 = $this->translationBuilder->create();
        $model_3 = $this->translationBuilder->create();

        $this->postGraphQL([
            'query' => $this->getQueryStr()
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        [
                            'key' => $model_1->key,
                            'place' => $model_1->place,
                            'lang' => $model_1->lang,
                            'text' => $model_1->text,
                        ],
                        ['key' => $model_2->key],
                        ['key' => $model_3->key],
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data.'.self::QUERY)
        ;

//        $this->assertNotNull(
//            Cache::get(CacheKeyEnum::TRANSLATIONS .'_'. hash_data(array_merge(['key', 'place', 'text', 'lang'], [])))
//        );
//
//        Cache::flush();
    }

    protected function getQueryStr(): string
    {
        return sprintf(
            '
            {
                %s {
                    key
                    place
                    text
                    lang
                }
            }',
            self::QUERY
        );
    }

    /** @test */
    public function success_filter_by_places(): void
    {
        $places = ['site', 'api'];

        /** @var $model_1 Translation */
        $model_1 = $this->translationBuilder
            ->place($places[0])
            ->create();
        $model_2 = $this->translationBuilder
            ->place($places[1])
            ->create();
        $model_3 = $this->translationBuilder->create();

        $this->postGraphQL([
            'query' => $this->getQueryStrByPlace($places)
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        ['key' => $model_1->key],
                        ['key' => $model_2->key],
                    ]
                ]
            ])
            ->assertJsonCount(2, 'data.'.self::QUERY)
        ;
    }

    protected function getQueryStrByPlace(array $data): string
    {
        return sprintf(
            '
            {
                %s (
                    place: ["%s", "%s"]
                ) {
                    key
                    place
                    text
                    lang
                }
            }',
            self::QUERY,
            $data[0],
            $data[1],
        );
    }

    /** @test */
    public function success_filter_by_lang(): void
    {
        $lang_1 = $this->languageBuilder->create();
        $lang_2 = $this->languageBuilder->create();

        $places = [$lang_1->slug, $lang_2->slug];

        /** @var $model_1 Translation */
        $model_1 = $this->translationBuilder
            ->lang($places[0])
            ->create();
        $model_2 = $this->translationBuilder
            ->lang($places[1])
            ->create();
        $model_3 = $this->translationBuilder->create();

        $this->postGraphQL([
            'query' => $this->getQueryStrByLang($places)
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        ['key' => $model_1->key],
                        ['key' => $model_2->key],
                    ]
                ]
            ])
            ->assertJsonCount(2, 'data.'.self::QUERY)
        ;
    }

    protected function getQueryStrByLang(array $data): string
    {
        return sprintf(
            '
            {
                %s (
                    lang: ["%s", "%s"]
                ) {
                    key
                    place
                    text
                    lang
                }
            }',
            self::QUERY,
            $data[0],
            $data[1],
        );
    }

    /** @test */
    public function success_search_by_key(): void
    {
        /** @var $model_1 Translation */
        $model_1 = $this->translationBuilder
            ->key('some.key.update')
            ->create();
        $model_2 = $this->translationBuilder
            ->key('some.update')
            ->create();
        $model_3 = $this->translationBuilder
            ->key('key')
            ->create();

        $this->postGraphQL([
            'query' => $this->getQueryStrByKey('key')
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        ['key' => $model_1->key],
                        ['key' => $model_3->key],
                    ]
                ]
            ])
            ->assertJsonCount(2, 'data.'.self::QUERY)
        ;
    }

    protected function getQueryStrByKey(string $data): string
    {
        return sprintf(
            '
            {
                %s (
                    key: "%s"
                ) {
                    key
                    place
                    text
                    lang
                }
            }',
            self::QUERY,
            $data
        );
    }

    /** @test */
    public function success_search_by_text(): void
    {
        /** @var $model_1 Translation */
        $model_1 = $this->translationBuilder
            ->text('some.key.update')
            ->create();
        $model_2 = $this->translationBuilder
            ->text('some.update')
            ->create();
        $model_3 = $this->translationBuilder
            ->text('key')
            ->create();

        $this->postGraphQL([
            'query' => $this->getQueryStrByText('key')
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        ['key' => $model_1->key],
                        ['key' => $model_3->key],
                    ]
                ]
            ])
            ->assertJsonCount(2, 'data.'.self::QUERY)
        ;
    }

    protected function getQueryStrByText(string $data): string
    {
        return sprintf(
            '
            {
                %s (
                    text: "%s"
                ) {
                    key
                    place
                    text
                    lang
                }
            }',
            self::QUERY,
            $data
        );
    }

    /** @test */
    public function sort_by_place(): void
    {
        /** @var $model_1 Translation */
        $model_1 = $this->translationBuilder
            ->place('site')
            ->create();
        $model_2 = $this->translationBuilder
            ->place('api')
            ->create();
        $model_3 = $this->translationBuilder
            ->place('admin')
            ->create();

        $this->postGraphQL([
            'query' => $this->getQueryStrSort("place-asc")
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        ['key' => $model_3->key],
                        ['key' => $model_2->key],
                        ['key' => $model_1->key],
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data.'.self::QUERY)
        ;

        $this->postGraphQL([
            'query' => $this->getQueryStrSort("place-desc")
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        ['key' => $model_1->key],
                        ['key' => $model_2->key],
                        ['key' => $model_3->key],
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data.'.self::QUERY)
        ;
    }

    /** @test */
    public function sort_by_key(): void
    {
        /** @var $model_1 Translation */
        $model_1 = $this->translationBuilder
            ->key('asd')
            ->create();
        $model_2 = $this->translationBuilder
            ->key('bapi')
            ->create();
        $model_3 = $this->translationBuilder
            ->key('admin')
            ->create();

        $this->postGraphQL([
            'query' => $this->getQueryStrSort("key-asc")
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        ['key' => $model_3->key],
                        ['key' => $model_1->key],
                        ['key' => $model_2->key],
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data.'.self::QUERY)
        ;

        $this->postGraphQL([
            'query' => $this->getQueryStrSort("key-desc")
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        ['key' => $model_2->key],
                        ['key' => $model_1->key],
                        ['key' => $model_3->key],
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data.'.self::QUERY)
        ;
    }

    /** @test */
    public function sort_by_lang(): void
    {
        $lang_1 = $this->languageBuilder->slug('fr')->create();
        $lang_2 = $this->languageBuilder->slug('uk')->create();

        /** @var $model_1 Translation */
        $model_1 = $this->translationBuilder
            ->lang($lang_1->slug)
            ->create();
        $model_2 = $this->translationBuilder
            ->lang($lang_2->slug)
            ->create();
        $model_3 = $this->translationBuilder
            ->create();

        $this->postGraphQL([
            'query' => $this->getQueryStrSort("lang-asc")
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        ['key' => $model_3->key],
                        ['key' => $model_1->key],
                        ['key' => $model_2->key],
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data.'.self::QUERY)
        ;

        $this->postGraphQL([
            'query' => $this->getQueryStrSort("lang-desc")
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        ['key' => $model_2->key],
                        ['key' => $model_1->key],
                        ['key' => $model_3->key],
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data.'.self::QUERY)
        ;
    }

    protected function getQueryStrSort(string $value): string
    {
        return sprintf(
            '
            {
                %s (sort: "%s") {
                    key
                    place
                    lang
                    text
                }
            }',
            self::QUERY,
            $value
        );
    }
}
