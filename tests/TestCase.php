<?php

namespace Tests;

use App\Helpers\DbConnections;
use App\Modules\User\Models\User;
use Core\Repositories\Passport\PassportClientRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\TestResponse;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Traits\Assert\AssertErrors;
use Tests\Traits\InteractsWithAuth;

abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    use CreatesApplication;
    use InteractsWithAuth;
    use AssertErrors;

    protected array $connectionsToTransact = [
        DbConnections::DEFAULT,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('grants.filter_enabled', false);
    }

    protected function postGraphQL(array $data, array $headers = []): TestResponse
    {
        return $this->postJson(config('graphql.route.prefix'), $data, $headers);
    }

    protected function postGraphQLBackOffice(array $data, array $headers = []): TestResponse
    {
        return $this->postJson(config('graphql.route.admin_prefix'), $data, $headers);
    }

    protected function postGraphQlUpload(array $data, array $headers = []): TestResponse
    {
        if (empty($headers)) {
            $headers = ['content-type' => 'multipart/form-data'];
        }

        return $this->post(config('graphql.route.prefix'), $data, $headers);
    }

    protected function postGraphQlBackOfficeUpload(array $data, array $headers = []): TestResponse
    {
        if (empty($headers)) {
            $headers = ['content-type' => 'multipart/form-data'];
        }

        return $this->post(config('graphql.route.admin_prefix'), $data, $headers);
    }

    protected function passportInit(): void
    {
        $this->artisan("passport:client --password --provider=admins --name='Admins'");
        $this->artisan("passport:client --password --provider=users --name='Users'");

        $adminPassportClient = $this->getPassportRepository()->findForAdmin();
        Config::set('auth.oauth_client.admins.id', $adminPassportClient->id);
        Config::set('auth.oauth_client.admins.secret', $adminPassportClient->secret);

        $userPassportClient = $this->getPassportRepository()->findForUser();
        Config::set('auth.oauth_client.users.id', $userPassportClient->id);
        Config::set('auth.oauth_client.users.secret', $userPassportClient->secret);
    }

    protected function langInit(string $slug = 'en'): void
    {
        if(isset($this->langBuilder) && $this->langBuilder instanceof LanguageBuilder){
            $builder = $this->langBuilder;
        } else {
            $builder = resolve(LanguageBuilder::class);
        }
        /** @var $builder LanguageBuilder */
        $builder->slug($slug)->default()->active()->create();
    }

    protected function getPassportRepository(): PassportClientRepository
    {
        return resolve(PassportClientRepository::class);
    }

    protected function assertUsersHas(array $data, $connection = null): static
    {
        return $this->assertDatabaseHas(User::TABLE, $data, $connection);
    }

    protected function assertUsersMissing(array $data, $connection = null): static
    {
        return $this->assertDatabaseMissing(User::TABLE, $data, $connection);
    }

    protected function assertPermission(TestResponse $result): void
    {
        $errors = $result->json('errors');

        self::assertEquals('authorization', data_get($errors, '0.extensions.category'));
        self::assertEquals("No permission", data_get($errors, '0.message'));
    }
    protected function assertUnauthorized(TestResponse $result): void
    {
        $errors = $result->json('errors');

        self::assertEquals('authorization', data_get($errors, '0.extensions.category'));
        self::assertEquals("Unauthorized", data_get($errors, '0.message'));
    }
}
