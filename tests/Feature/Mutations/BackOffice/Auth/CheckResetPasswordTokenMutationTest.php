<?php

namespace Tests\Feature\Mutations\BackOffice\Auth;

use App\GraphQL\Mutations\BackOffice\Auth\CheckResetPasswordTokenMutation;
use App\Modules\Admin\Models\Admin;
use App\Traits\Auth\CryptToken;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\TestCase;

class CheckResetPasswordTokenMutationTest extends TestCase
{
    use DatabaseTransactions;
    use CryptToken;

    public const MUTATION = CheckResetPasswordTokenMutation::NAME;

    private AdminBuilder $adminBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->langInit();

        $this->adminBuilder = resolve(AdminBuilder::class);
    }

    /** @test */
    public function success_check_token(): void
    {
        /** @var $model Admin */
        $model = $this->adminBuilder->create();

        $token = $this->encryptToken($model);

        CarbonImmutable::setTestNow(CarbonImmutable::now()->addMinutes(10));

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($token)
        ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    self::MUTATION => true
                ]
            ])
        ;
    }

    /** @test */
    public function fail_wrong_token(): void
    {
        /** @var $model Admin */
        $model = $this->adminBuilder->create();

        $token = $this->encryptToken($model);

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr('ewrwerwer')
        ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    self::MUTATION => false
                ]
            ])
        ;
    }

    /** @test */
    public function fail_expired_tim(): void
    {
        /** @var $model Admin */
        $model = $this->adminBuilder->create();

        $token = $this->encryptToken($model);

        CarbonImmutable::setTestNow(CarbonImmutable::now()->addDay());

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($token)
        ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    self::MUTATION => false
                ]
            ])
        ;
    }

    protected function getQueryStr(string $value): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    token: "%s",
                )
            }',
            self::MUTATION,
            $value,
        );
    }
}
