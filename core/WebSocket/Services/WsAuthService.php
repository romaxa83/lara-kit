<?php

namespace Core\WebSocket\Services;

use App\Models\Admins\Admin;
use App\Models\Passport\Client;
use App\Models\Passport\Token;
use App\Modules\User\Models\User;
use Carbon\Carbon;
use Core\Models\BaseAuthenticatable;
use Core\WebSocket\Contracts\Subscribable;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;

abstract class WsAuthService
{
    /**
     * @todo suggest a better option
     */
    private const USER_TYPES = [
        'Users' => User::class,
        'Admins' => Admin::class,
    ];

    public function getUserByBearer(string $bearer): BaseAuthenticatable|Authenticatable|Subscribable|null
    {
        $bearerToken = explode(" ", $bearer);

        if (empty($bearerToken[1])) {
            return null;
        }

        $token = explode('.', $bearerToken[1]);

        if (empty($token[1])) {
            return null;
        }

        try {
            $token = json_to_array(base64_decode($token[1]));
        } catch (Exception) {
            return null;
        }

        if (empty($token['jti']) || empty($token['exp'])) {
            return null;
        }

        if ($token['exp'] < Carbon::now('UTC')
                ->getTimestamp()) {
            return null;
        }

        $token = Token::find($token['jti']);

        if (!$token) {
            return null;
        }

        $client = Client::find($token->client_id);

        if (!$client || !array_key_exists($client->name, self::USER_TYPES)) {
            return null;
        }

        /** @var BaseAuthenticatable $class */
        $class = self::USER_TYPES[$client->name];

        return $class::query()->find($token->user_id);
    }
}
