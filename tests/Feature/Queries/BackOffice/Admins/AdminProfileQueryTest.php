<?php

namespace Tests\Feature\Queries\BackOffice\Admins;

use App\GraphQL\Queries\BackOffice;
use App\GraphQL\Queries\BackOffice\Admins\AdminProfileQuery;
use App\Modules\Admin\Models\Admin;
use App\Modules\Permissions\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\Builders\Permissions\RoleBuilder;
use Tests\TestCase;

class AdminProfileQueryTest extends TestCase
{
    use DatabaseTransactions;

    public const QUERY = AdminProfileQuery::NAME;

    protected AdminBuilder $adminBuilder;
    protected RoleBuilder $roleBuilder;
    protected PermissionBuilder $permissionBuilder;

    public function setUp(): void
    {
        parent::setUp();
        $this->adminBuilder = resolve(AdminBuilder::class);
        $this->roleBuilder = resolve(RoleBuilder::class);
        $this->permissionBuilder = resolve(PermissionBuilder::class);

        $this->langInit();
    }

    /** @test */
    public function success_get_profile(): void
    {
        $perm_1 = $this->permissionBuilder->withTranslation()->create();
        $perm_2 = $this->permissionBuilder->withTranslation()->create();
        $perm_3 = $this->permissionBuilder->withTranslation()->create();

        /** @var $role Role */
        $role = $this->roleBuilder
            ->asAdmin()
            ->withTranslation()
            ->permissions($perm_1, $perm_2, $perm_3)
            ->create();

        /** @var $model Admin */
        $model = $this->adminBuilder
            ->phone(verify: true)
            ->role($role)
            ->create();

        $this->loginAsAdmin($model);

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr()
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'id' => $model->id,
                        'name' => $model->name,
                        'email' => $model->getEmail(),
                        'email_verified' => $model->isEmailVerified(),
                        'phone' => $model->getPhone(),
                        'phone_verified' => $model->isPhoneVerified(),
                        'role' => [
                            'id' => $model->role->id,
                        ],
                        'roles' => [
                            ['id' => $model->role->id]
                        ],
                        'lang' => $model->lang,
                        'language' => [
                            'name' => $model->language->name,
                            'slug' => $model->language->slug,
                        ],
                        'permissions' => [
                            [
                                'name' => $perm_1->name,
                                'translation' => [
                                    'title' => $perm_1->translation->title
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            ->assertJsonCount(1, 'data.'.self::QUERY.'.roles')
            ->assertJsonCount(3, 'data.'.self::QUERY.'.permissions')
            ->assertJsonCount(1, 'data.'.self::QUERY.'.permissions.0.translations')
            ->assertJsonCount(1, 'data.'.self::QUERY.'.permissions.1.translations')
            ->assertJsonCount(1, 'data.'.self::QUERY.'.permissions.2.translations')
        ;
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

    protected function getQueryStr(): string
    {
        return sprintf(
            '
            {
                %s {
                    id
                    name
                    email
                    email_verified
                    phone
                    phone_verified
                    role {
                        id
                    }
                    roles {
                        id
                    }
                    permissions {
                        name
                        translation {
                            title
                        }
                        translations {
                            title
                        }
                    }
                    lang
                    language {
                        name
                        slug
                    }
                }
            }',
            self::QUERY
        );
    }
}
