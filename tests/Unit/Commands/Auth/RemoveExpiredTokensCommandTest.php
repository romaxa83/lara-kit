<?php

namespace Tests\Unit\Commands\Auth;

use App\Modules\User\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Laravel\Passport\Client;
use Laravel\Passport\Token;
use Tests\TestCase;

class RemoveExpiredTokensCommandTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->passportInit();

        $this->langInit();
    }

    public function test_remove_expired_tokens_command(): void
    {
        $users = User::factory()->times(100)->create()->pluck('id');

        $clients = Client::query()->pluck('id');

        $insert = [];

        for ($i = 0; $i < 100; $i++) {
            $expires = $i < 50 ? now()->addDays(2) : now()->addDays(4);

            $insert[] = [
                'id' => Str::uuid()->toString(),
                'user_id' => $users->random(),
                'client_id' => $clients->random(),
                'revoked' => false,
                'created_at' => now(),
                'updated_at' => now(),
                'expires_at' => $expires
            ];
        }

        Token::query()->insert($insert);

        $this->artisan('auth:remove-expired-tokens');
        $this->assertDatabaseCount('oauth_access_tokens', 100);

        Carbon::setTestNow(now()->addDays(2));
        $this->artisan('auth:remove-expired-tokens');
        $this->assertDatabaseCount('oauth_access_tokens', 50);

        Carbon::setTestNow(now()->addDays(2));
        $this->artisan('auth:remove-expired-tokens');
        $this->assertDatabaseCount('oauth_access_tokens', 0);
    }
}
