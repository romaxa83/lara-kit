<?php

namespace Tests\Feature\Queries\Common\Localization;

use App\Enums\CacheKeyEnum;
use App\GraphQL\Queries\BackOffice;
use App\GraphQL\Queries\Common\Localization\LanguagesQuery;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\TestCase;

class LanguagesQueryTest extends TestCase
{
    use DatabaseTransactions;

    public const QUERY = LanguagesQuery::NAME;

    protected LanguageBuilder $languageBuilder;

    public function setUp(): void
    {
        parent::setUp();
        $this->languageBuilder = resolve(LanguageBuilder::class);
    }

    /** @test */
    public function success_get_langs(): void
    {
//        $this->assertNull(Cache::get(CacheKeyEnum::LANGUAGE . '_slug_name_active_default_sort_'));

        $lang_1 = $this->languageBuilder->default()->sort(2)->create();
        $lang_2 = $this->languageBuilder->sort(1)->create();
        $lang_3 = $this->languageBuilder->sort(3)->create();

        $this->postGraphQL([
            'query' => $this->getQueryStr()
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        [
                            'slug' => $lang_2->slug,
                            'name' => $lang_2->name,
                            'active' => $lang_2->active,
                            'default' => $lang_2->default,
                            'sort' => $lang_2->sort,
                        ],
                        ['slug' => $lang_1->slug],
                        ['slug' => $lang_3->slug],
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data.'.self::QUERY)
        ;

//        $this->assertNotNull(Cache::get(CacheKeyEnum::LANGUAGE . '_slug_name_active_default_sort_'));

//        Cache::flush();
    }

    protected function getQueryStr(): string
    {
        return sprintf(
            '
            {
                %s {
                    slug
                    name
                    active
                    default
                    sort
                }
            }',
            self::QUERY
        );
    }

    /** @test */
    public function filter_by_active(): void
    {
        $lang_1 = $this->languageBuilder->default()->sort(1)->create();
        $lang_2 = $this->languageBuilder->sort(2)->create();
        $lang_3 = $this->languageBuilder->sort(3)->active(false)->create();

        $this->postGraphQL([
            'query' => $this->getQueryStrByActive("true")
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        ['slug' => $lang_1->slug],
                        ['slug' => $lang_2->slug],
                    ]
                ]
            ])
            ->assertJsonCount(2, 'data.'.self::QUERY)
        ;
    }

    protected function getQueryStrByActive(string $value): string
    {
        return sprintf(
            '
            {
                %s (active: %s) {
                    slug
                    name
                    active
                    default
                    sort
                }
            }',
            self::QUERY,
            $value
        );
    }

    /** @test */
    public function sort_by_sort(): void
    {
        $lang_1 = $this->languageBuilder->default()->sort(1)->create();
        $lang_2 = $this->languageBuilder->sort(2)->create();
        $lang_3 = $this->languageBuilder->sort(3)->active(false)->create();

        $this->postGraphQL([
            'query' => $this->getQueryStrSort("sort-asc")
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        ['slug' => $lang_1->slug],
                        ['slug' => $lang_2->slug],
                        ['slug' => $lang_3->slug],
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data.'.self::QUERY)
        ;

        $this->postGraphQL([
            'query' => $this->getQueryStrSort("sort-desc")
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        ['slug' => $lang_3->slug],
                        ['slug' => $lang_2->slug],
                        ['slug' => $lang_1->slug],
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
                    slug
                    name
                    active
                    default
                    sort
                }
            }',
            self::QUERY,
            $value
        );
    }
}
