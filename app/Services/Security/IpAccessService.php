<?php

namespace App\Services\Security;

use App\Dto\Security\IpAccessDto;
use App\Models\Security\IpAccess;
use Core\ValueObjects\IpAddressValueObject;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

class IpAccessService
{
    public function create(IpAccessDto $dto): IpAccess
    {
        $ipAccess = new IpAccess();

        $this->fill($dto, $ipAccess);

        $ipAccess->save();

        $this->flushCache();

        return $ipAccess;
    }

    public function fill(IpAccessDto $build, IpAccess $ipAccess): void
    {
        $ipAccess->address = $build->getAddress();
        $ipAccess->description = $build->getDescription();
        $ipAccess->active = $build->isActive();
    }

    public function flushCache(): void
    {
        $this->cache()->flush();
    }

    public function cache(): TaggedCache
    {
        return Cache::tags([IpAccess::class]);
    }

    public function update(IpAccess $ipAccess, IpAccessDto $dto): IpAccess
    {
        $this->fill($dto, $ipAccess);

        $ipAccess->save();

        $this->flushCache();

        return $ipAccess;
    }

    public function delete(array $ids): void
    {
        IpAccess::query()
            ->whereKey($ids)
            ->delete();

        $this->flushCache();
    }

    public function check(IpAddressValueObject $ip): bool
    {
        return in_array((string)$ip, $this->getAllowedIpAddresses(), true);
    }

    public function getAllowedIpAddresses(): array
    {
        $fromDb = $this->cache()->remember(
            'allowed_ip_addresses_from_db',
            config('security.ip-access.cache.duration'),
            fn() => IpAccess::query()
                ->active()
                ->pluck('address')
                ->toArray()
        );

        $fromEnv = config('security.ip-access.list');

        $allowed = array_merge(
            $fromDb,
            $fromEnv
        );

        return array_unique($allowed);
    }
}
