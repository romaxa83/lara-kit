<?php

namespace Core\Repositories\Passport;

use App\Models\Passport\Client;
use Illuminate\Database\Eloquent\Builder;

class PassportClientRepository
{
    public function findForAdmin(): ?Client
    {
        return $this->findFor('admins');
    }

    public function findFor(string $provider): ?Client
    {
        return $this->query()
            ->where('provider', $provider)
            ->where('password_client', 1)
            ->where('revoked', 0)
            ->first();
    }

    public function query(): Builder|Client
    {
        return Client::query();
    }

    public function findForUser(): ?Client
    {
        return $this->findFor('users');
    }
}
