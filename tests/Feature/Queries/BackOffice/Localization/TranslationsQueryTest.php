<?php

namespace Tests\Feature\Queries\BackOffice\Localization;

use App\GraphQL\Queries\BackOffice;
use App\GraphQL\Queries\BackOffice\Localization\TranslationsQuery;
use App\Modules\Localization\Models\Translation;
use App\Modules\Permissions\Enums\Guard;
use App\Permissions\Localization\Translation\TranslationListPermission;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Localization\TranslationBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\TestCase;

class TranslationsQueryTest extends TestCase
{
    use DatabaseTransactions;

    public const QUERY = TranslationsQuery::NAME;

    protected LanguageBuilder $languageBuilder;
    protected TranslationBuilder $translationBuilder;
    protected PermissionBuilder $permissionBuilder;
    protected AdminBuilder $adminBuilder;

    public function setUp(): void
    {
        parent::setUp();
        $this->languageBuilder = resolve(LanguageBuilder::class);
        $this->translationBuilder = resolve(TranslationBuilder::class);
        $this->adminBuilder = resolve(AdminBuilder::class);
        $this->permissionBuilder = resolve(PermissionBuilder::class);

        $this->langInit();
    }

    /** @test */
    public function success_paginator(): void
    {
        $this->loginAsSuperAdmin();

        /** @var $model_1 Translation */
        $model_1 = $this->translationBuilder->create();
        $model_2 = $this->translationBuilder->create();
        $model_3 = $this->translationBuilder->create();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr()
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            [
                                'key' => $model_1->key,
                                'place' => $model_1->place,
                                'lang' => $model_1->lang,
                                'text' => $model_1->text,
                            ],
                            ['key' => $model_2->key],
                            ['key' => $model_3->key],
                        ],
                        'meta' => [
                            'total' => 3
                        ],
                    ]
                ]
            ])
        ;
    }

    /** @test */
    public function success_paginator_as_admin(): void
    {
        $perm = $this->permissionBuilder
            ->guard(Guard::ADMIN())
            ->name(TranslationListPermission::KEY)->create();
        $this->loginAsAdmin(null, $perm);

        /** @var $model_1 Translation */
        $model_1 = $this->translationBuilder->create();
        $model_2 = $this->translationBuilder->create();
        $model_3 = $this->translationBuilder->create();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr()
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['key' => $model_1->key],
                            ['key' => $model_2->key],
                            ['key' => $model_3->key],
                        ],
                        'meta' => [
                            'total' => 3
                        ],
                    ]
                ]
            ])
        ;
    }

    protected function getQueryStr(): string
    {
        return sprintf(
            '
            {
                %s {
                    data {
                        key
                        place
                        lang
                        text
                    }
                    meta {
                        total
                        per_page
                        current_page
                        from
                        to
                        last_page
                        has_more_pages
                    }
                }
            }',
            self::QUERY
        );
    }

    /** @test */
    public function success_with_page(): void
    {
        $this->loginAsSuperAdmin();

        /** @var $model_1 Translation */
        $model_1 = $this->translationBuilder->create();
        $model_2 = $this->translationBuilder->create();
        $model_3 = $this->translationBuilder->create();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrWithPage(2)
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'meta' => [
                            'total' => 3,
                            'per_page' => 10,
                            'current_page' => 2,
                            'from' => null,
                            'to' => null,
                            'last_page' => 1,
                            'has_more_pages' => false,
                        ],
                    ]
                ]
            ])
        ;
    }

    protected function getQueryStrWithPage($page): string
    {
        return sprintf(
            '
            {
                %s (page: %s) {
                    data {
                        key
                    }
                    meta {
                        total
                        per_page
                        current_page
                        from
                        to
                        last_page
                        has_more_pages
                    }
                }
            }',
            self::QUERY,
            $page
        );
    }

    /** @test */
    public function success_with_per_page(): void
    {
        $this->loginAsSuperAdmin();

        /** @var $model_1 Translation */
        $model_1 = $this->translationBuilder->create();
        $model_2 = $this->translationBuilder->create();
        $model_3 = $this->translationBuilder->create();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrWithPerPage(2)
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'meta' => [
                            'total' => 3,
                            'per_page' => 2,
                            'current_page' => 1,
                            'from' => 1,
                            'to' => 2,
                            'last_page' => 2,
                            'has_more_pages' => true,
                        ],
                    ]
                ]
            ])
        ;
    }

    protected function getQueryStrWithPerPage($perPage): string
    {
        return sprintf(
            '
            {
                %s (per_page: %s) {
                    data {
                        key
                    }
                    meta {
                        total
                        per_page
                        current_page
                        from
                        to
                        last_page
                        has_more_pages
                    }
                }
            }',
            self::QUERY,
            $perPage
        );
    }

    /** @test */
    public function success_empty(): void
    {
        $this->loginAsSuperAdmin();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr()
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'meta' => [
                            'total' => 0
                        ],
                    ]
                ]
            ])
        ;
    }

    /** @test */
    public function success_filter_by_places(): void
    {
        $this->loginAsSuperAdmin();

        $places = ['site', 'api'];

        /** @var $model_1 Translation */
        $model_1 = $this->translationBuilder
            ->place($places[0])
            ->create();
        $model_2 = $this->translationBuilder
            ->place($places[1])
            ->create();
        $model_3 = $this->translationBuilder->create();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrByPlace($places)
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['key' => $model_1->key],
                            ['key' => $model_2->key],
                        ],
                        'meta' => [
                            'total' => 2
                        ]
                    ]
                ]
            ])
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
                    data {
                        key
                    }
                    meta {
                        total
                    }
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
        $this->loginAsSuperAdmin();

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

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrByLang($places)
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['key' => $model_1->key],
                            ['key' => $model_2->key],
                        ],
                        'meta' => [
                            'total' => 2
                        ]
                    ]
                ]
            ])
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
                    data {
                        key
                    }
                    meta {
                        total
                    }
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
        $this->loginAsSuperAdmin();

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

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrByKey('key')
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['key' => $model_1->key],
                            ['key' => $model_3->key],
                        ],
                        'meta' => [
                            'total' => 2
                        ]
                    ]
                ]
            ])
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
                    data {
                        key
                    }
                    meta {
                        total
                    }
                }
            }',
            self::QUERY,
            $data
        );
    }

    /** @test */
    public function success_search_by_text(): void
    {
        $this->loginAsSuperAdmin();

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

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrByText('key')
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['key' => $model_1->key],
                            ['key' => $model_3->key],
                        ],
                        'meta' => [
                            'total' => 2
                        ]
                    ]
                ]
            ])
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
                     data {
                        key
                    }
                    meta {
                        total
                    }
                }
            }',
            self::QUERY,
            $data
        );
    }

    /** @test */
    public function sort_by_place(): void
    {
        $this->loginAsSuperAdmin();

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

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrSort("place-asc")
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['key' => $model_3->key],
                            ['key' => $model_2->key],
                            ['key' => $model_1->key],
                        ],
                        'meta' => [
                            'total' => 3
                        ]
                    ]
                ]
            ])
        ;

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrSort("place-desc")
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['key' => $model_1->key],
                            ['key' => $model_2->key],
                            ['key' => $model_3->key],
                        ],
                        'meta' => [
                            'total' => 3
                        ]
                    ]
                ]
            ])
        ;
    }

    /** @test */
    public function sort_by_key(): void
    {
        $this->loginAsSuperAdmin();

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

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrSort("key-asc")
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['key' => $model_3->key],
                            ['key' => $model_1->key],
                            ['key' => $model_2->key],
                        ],
                        'meta' => [
                            'total' => 3
                        ]
                    ]
                ]
            ])
        ;

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrSort("key-desc")
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['key' => $model_2->key],
                            ['key' => $model_1->key],
                            ['key' => $model_3->key],
                        ],
                        'meta' => [
                            'total' => 3
                        ]
                    ]
                ]
            ])
        ;
    }

    /** @test */
    public function sort_by_lang(): void
    {
        $this->loginAsSuperAdmin();

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

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrSort("lang-asc")
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['key' => $model_3->key],
                            ['key' => $model_1->key],
                            ['key' => $model_2->key],
                        ],
                        'meta' => [
                            'total' => 3
                        ]
                    ]
                ]
            ])
        ;

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrSort("lang-desc")
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['key' => $model_2->key],
                            ['key' => $model_1->key],
                            ['key' => $model_3->key],
                        ],
                        'meta' => [
                            'total' => 3
                        ]
                    ]
                ]
            ])
        ;
    }

    protected function getQueryStrSort(string $value): string
    {
        return sprintf(
            '
            {
                %s (sort: "%s") {
                     data {
                        key
                    }
                    meta {
                        total
                    }
                }
            }',
            self::QUERY,
            $value
        );
    }

    /** @test */
    public function not_perm(): void
    {
        $this->loginAsAdmin();

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr()
        ])
        ;

        $this->assertPermission($res);
    }

    /** @test */
    public function not_auth(): void
    {
        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr()
        ])
        ;

        $this->assertUnauthorized($res);
    }
}
