<?php

namespace App\Providers;

use App\Modules\Utils\Phones\Services\SmsSender\ArraySender;
use App\Modules\Utils\Phones\Services\SmsSender\SmsSender;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SmsSender::class, function (Application $app) {
            $config = $app->make('config')->get('sms');

            switch ($config['driver']) {

                case 'array':
                    return new ArraySender();
                default:
                    throw new \InvalidArgumentException('Undefined SMS driver ' . $config['driver']);
            }
        });
    }
}

