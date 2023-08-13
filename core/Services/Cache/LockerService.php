<?php

namespace Core\Services\Cache;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Cache;

class LockerService
{
    public function isQueueInProcess(ShouldBeUnique $job): bool
    {
        $postfix = method_exists($job, 'uniqueId')
            ? $job->uniqueId()
            : '';

        $lock = Cache::lock(
            'laravel_unique_job:' . get_class($job) . $postfix,
            10
        );

        if ($lock->get()) {
            $lock->release();

            return false;
        }

        return true;
    }
}
