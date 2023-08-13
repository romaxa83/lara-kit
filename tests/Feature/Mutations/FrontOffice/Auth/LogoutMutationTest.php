<?php

namespace Tests\Feature\Mutations\FrontOffice\Auth;

use App\GraphQL\Mutations\FrontOffice\Auth\LoginMutation;
use App\GraphQL\Mutations\FrontOffice\Auth\LogoutMutation;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;

class LogoutMutationTest extends TestCase
{
    use DatabaseTransactions;

    public const MUTATION = LogoutMutation::NAME;

    public UserBuilder $userBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userBuilder = resolve(UserBuilder::class);

        $this->passportInit();
        $this->langInit();
    }

    /** @test */
    public function success_logout(): void
    {
        /** @var $model User */
        $model = $this->userBuilder->create();

        $res = $this->postGraphQL([
            'query' => LoginMutationTest::getQueryStr([
                'email' => $model->email,
                'password' => 'password',
                'remember_me' => 'true'
            ])
        ])
        ;

        [LoginMutation::NAME => $data] = $res->json('data');

        $this->postGraphQL([
            'query' => $this->getQueryStr()
        ],[
            'Authorization' => 'Bearer ' . $data['access_token']
        ])
            ->assertOk()
            ->assertJson(['data' => [self::MUTATION => true,]]);

        $this->postGraphQL([
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
