<?php

namespace Tests\Feature\Queries\BackOffice\Admins;

use App\GraphQL\Queries\BackOffice;
use App\GraphQL\Queries\BackOffice\Admins\AdminsQuery;
use App\Modules\Admin\Models\Admin;
use App\Modules\Permissions\Enums\Guard;
use App\Permissions\Admins\AdminListPermission;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\TestCase;

class AdminsQueryTest extends TestCase
{
    use DatabaseTransactions;

    public const QUERY = AdminsQuery::NAME;

    protected AdminBuilder $adminBuilder;
    protected PermissionBuilder $permissionBuilder;

    public function setUp(): void
    {
        parent::setUp();
        $this->adminBuilder = resolve(AdminBuilder::class);
        $this->permissionBuilder = resolve(PermissionBuilder::class);

        $this->langInit();
    }

    /** @test */
    public function success_paginator(): void
    {
        $this->loginAsSuperAdmin();

        $m_1 = $this->adminBuilder->create();
        $m_2 = $this->adminBuilder->create();
        $m_3 = $this->adminBuilder->create();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr()
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['id' => $m_3->id],
                            ['id' => $m_2->id],
                            ['id' => $m_1->id],
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
            ->name(AdminListPermission::KEY)->create();
        $model = $this->loginAsAdmin(null, $perm);

        $m_1 = $this->adminBuilder->create();
        $m_2 = $this->adminBuilder->create();
        $m_3 = $this->adminBuilder->create();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr()
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['id' => $m_3->id],
                            ['id' => $m_2->id],
                            ['id' => $m_1->id],
                            ['id' => $model->id],
                        ],
                        'meta' => [
                            'total' => 4
                        ],
                    ]
                ]
            ])
        ;
    }

    /** @test */
    public function success_with_page(): void
    {
        $this->loginAsSuperAdmin();

        $this->adminBuilder->create();
        $this->adminBuilder->create();
        $this->adminBuilder->create();

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
                        id
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

        $this->adminBuilder->create();
        $this->adminBuilder->create();
        $this->adminBuilder->create();

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
                        id
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

    protected function getQueryStr(): string
    {
        return sprintf(
            '
            {
                %s {
                    data {
                        id
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
    public function filter_by_id(): void
    {
        $this->loginAsSuperAdmin();

        /** @var $model Admin */
        $model = $this->adminBuilder->create();
        $this->adminBuilder->create();
        $this->adminBuilder->create();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrById($model->id)
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            [
                                'id' => $model->id,
                                'name' => $model->name,
                            ]
                        ],
                        'meta' => [
                            'total' => 1
                        ],
                    ]
                ]
            ])
        ;
    }

    protected function getQueryStrById($id): string
    {
        return sprintf(
            '
            {
                %s (id: %s){
                    data {
                        id
                        name
                    }
                    meta {
                        total
                    }
                }
            }',
            self::QUERY,
            $id
        );
    }

    /** @test */
    public function filter_by_ids(): void
    {
        $this->loginAsSuperAdmin();

        /** @var $model_1 Admin */
        $model_1 = $this->adminBuilder->create();
        $model_2 = $this->adminBuilder->create();
        $this->adminBuilder->create();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrByIds([$model_1->id, $model_2->id])
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['id' => $model_2->id],
                            ['id' => $model_1->id],
                        ],
                        'meta' => [
                            'total' => 2
                        ],
                    ]
                ]
            ])
        ;
    }

    protected function getQueryStrByIds(array $ids): string
    {
        return sprintf(
            '
            {
                %s (ids: [%s, %s]){
                    data {
                        id
                    }
                    meta {
                        total
                    }
                }
            }',
            self::QUERY,
            $ids[0],
            $ids[1],
        );
    }

    /** @test */
    public function filter_by_phone(): void
    {
        $this->loginAsSuperAdmin();

        /** @var $model_1 Admin */
        $model_1 = $this->adminBuilder->phone("380954515555")->create();
        $model_2 = $this->adminBuilder->phone("380954525555")->create();
        $this->adminBuilder->phone("380956505555")->create();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrByPhone("3809545")
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['id' => $model_2->id],
                            ['id' => $model_1->id],
                        ],
                        'meta' => [
                            'total' => 2
                        ],
                    ]
                ]
            ])
        ;
    }

    protected function getQueryStrByPhone(string $value): string
    {
        return sprintf(
            '
            {
                %s (phone: "%s"){
                    data {
                        id
                    }
                    meta {
                        total
                    }
                }
            }',
            self::QUERY,
            $value,
        );
    }

    /** @test */
    public function search(): void
    {
        $this->loginAsSuperAdmin();

        /** @var $model_1 Admin */
        $model_1 = $this->adminBuilder->setData(['name' => 'termtuu'])->create();
        $model_2 = $this->adminBuilder->setData(['email' => 'weterm@gamil.com'])->create();
        $this->adminBuilder->setData([
            'name' => 'name',
            'email' => 'test@gmail.com'
        ])->create();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrBySearch('term')
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['id' => $model_2->id],
                            ['id' => $model_1->id],
                        ],
                        'meta' => [
                            'total' => 2
                        ],
                    ]
                ]
            ])
        ;
    }

    protected function getQueryStrBySearch(string $value): string
    {
        return sprintf(
            '
            {
                %s (query: "%s"){
                    data {
                        id
                    }
                    meta {
                        total
                    }
                }
            }',
            self::QUERY,
            $value,
        );
    }

    /** @test */
    public function sort_by_id(): void
    {
        $this->loginAsSuperAdmin();

        /** @var $model_1 Admin */
        $model_1 = $this->adminBuilder->create();
        $model_2 = $this->adminBuilder->create();
        $model_3 = $this->adminBuilder->create();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrBySort('id-desc')
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['id' => $model_3->id],
                            ['id' => $model_2->id],
                            ['id' => $model_1->id],
                        ],
                        'meta' => [
                            'total' => 3
                        ],
                    ]
                ]
            ])
        ;

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrBySort('id-asc')
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['id' => $model_1->id],
                            ['id' => $model_2->id],
                            ['id' => $model_3->id],
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
    public function sort_by_name(): void
    {
        $this->loginAsSuperAdmin();

        /** @var $model_1 Admin */
        $model_1 = $this->adminBuilder->setData(['name' => 'ben'])->create();
        $model_2 = $this->adminBuilder->setData(['name' => 'allan'])->create();
        $model_3 = $this->adminBuilder->setData(['name' => 'zet'])->create();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrBySort('name-asc')
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['id' => $model_2->id],
                            ['id' => $model_1->id],
                            ['id' => $model_3->id],
                        ],
                        'meta' => [
                            'total' => 3
                        ],
                    ]
                ]
            ])
        ;

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrBySort('name-desc')
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['id' => $model_3->id],
                            ['id' => $model_1->id],
                            ['id' => $model_2->id],
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
    public function sort_by_email(): void
    {
        $this->loginAsSuperAdmin();

        /** @var $model_1 Admin */
        $model_1 = $this->adminBuilder->setData(['email' => 'ben@gmail.com'])->create();
        $model_2 = $this->adminBuilder->setData(['email' => 'allan@gmail.com'])->create();
        $model_3 = $this->adminBuilder->setData(['email' => 'zet@gmail.com'])->create();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrBySort('email-asc')
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['id' => $model_2->id],
                            ['id' => $model_1->id],
                            ['id' => $model_3->id],
                        ],
                        'meta' => [
                            'total' => 3
                        ],
                    ]
                ]
            ])
        ;

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrBySort('email-desc')
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['id' => $model_3->id],
                            ['id' => $model_1->id],
                            ['id' => $model_2->id],
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
    public function sort_by_created(): void
    {
        $this->loginAsSuperAdmin();

        $data = CarbonImmutable::now()->subMonth();

        /** @var $model_1 Admin */
        $model_1 = $this->adminBuilder->setData(['created_at' => $data->addDays(10)])->create();
        $model_2 = $this->adminBuilder->setData(['created_at' => $data->addDays(4)])->create();
        $model_3 = $this->adminBuilder->setData(['created_at' => $data->addDays(2)])->create();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrBySort('created_at-asc')
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['id' => $model_3->id],
                            ['id' => $model_2->id],
                            ['id' => $model_1->id],
                        ],
                        'meta' => [
                            'total' => 3
                        ],
                    ]
                ]
            ])
        ;

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrBySort('created_at-desc')
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'data' => [
                            ['id' => $model_1->id],
                            ['id' => $model_2->id],
                            ['id' => $model_3->id],
                        ],
                        'meta' => [
                            'total' => 3
                        ],
                    ]
                ]
            ])
        ;
    }

    protected function getQueryStrBySort(string $value): string
    {
        return sprintf(
            '
            {
                %s (sort: "%s"){
                    data {
                        id
                    }
                    meta {
                        total
                    }
                }
            }',
            self::QUERY,
            $value,
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
