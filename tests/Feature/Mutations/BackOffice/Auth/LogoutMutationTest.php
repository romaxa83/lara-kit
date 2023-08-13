<?php

namespace Tests\Feature\Mutations\BackOffice\Auth;

use App\GraphQL\Mutations\BackOffice\Auth\LoginMutation;
use App\GraphQL\Mutations\BackOffice\Auth\LogoutMutation;
use App\Modules\Admin\Models\Admin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\TestCase;

class LogoutMutationTest extends TestCase
{
    use DatabaseTransactions;

    public const MUTATION = LogoutMutation::NAME;

    public AdminBuilder $adminBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminBuilder = resolve(AdminBuilder::class);

        $this->passportInit();
        $this->langInit();
    }

    /** @test */
    public function success_logout(): void
    {
        /** @var $model Admin */
        $model = $this->adminBuilder->create();

        $res = $this->postGraphQLBackOffice([
            'query' => LoginMutationTest::getQueryStr([
                'email' => $model->email,
                'password' => 'password'
            ])
        ])
        ;

        [LoginMutation::NAME => $data] = $res->json('data');

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr()
        ],[
            'Authorization' => 'Bearer ' . $data['access_token']
        ])
            ->assertOk()
            ->assertJson(['data' => [self::MUTATION => true,]]);

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr()
        ],[
            'Authorization' => 'Bearer ' . $data['access_token']
        ])
            ->assertOk()
            ->assertJson(['data' => [self::MUTATION => false,]]);
    }

    public static function getQueryStr(): string
    {
        return sprintf(
            'mutation {%s}',
            self::MUTATION,
        );
    }
}

