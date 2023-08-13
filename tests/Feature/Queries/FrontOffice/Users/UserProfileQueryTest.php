<?php

namespace Tests\Feature\Queries\FrontOffice\Users;

use App\GraphQL\Queries\BackOffice;
use App\GraphQL\Queries\FrontOffice\Users\UserProfileQuery;
use App\Modules\Permissions\Models\Role;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\Builders\Permissions\RoleBuilder;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;

class UserProfileQueryTest extends TestCase
{
    use DatabaseTransactions;

    public const QUERY = UserProfileQuery::NAME;

    protected UserBuilder $userBuilder;
    protected RoleBuilder $roleBuilder;
    protected PermissionBuilder $permissionBuilder;

    public function setUp(): void
    {
        parent::setUp();
        $this->userBuilder = resolve(UserBuilder::class);
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
            ->asUser()
            ->withTranslation()
            ->permissions($perm_1, $perm_2, $perm_3)
            ->create();

        /** @var $model User */
        $model = $this->userBuilder
            ->phone(verify: true)
            ->create();

        $this->loginAsUser($model);

        $this->postGraphQL([
            'query' => $this->getQueryStr()
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        'id' => $model->id,
                        'name' => $model->name,
                        'email' => $model->getEmail()->asString(),
                        'email_verified' => $model->isEmailVerified(),
                        'phone' => $model->getPhone()->asString(),
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
        $res = $this->postGraphQL([
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
