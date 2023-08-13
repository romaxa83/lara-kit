<?php

namespace App\Console\Commands;

use App\Modules\Localization\Repositories\LanguageRepository;
use App\Modules\Permissions\Repositories\PermissionRepository;
use Illuminate\Console\Command;

class CheckTime extends Command
{
    protected $signature = 'app:check_time';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        /** @var $repo PermissionRepository */
        $repo = resolve(PermissionRepository::class);
        try {
            $start = microtime(true);

            $res = $repo->getPermissionsIdByKey([
                'admin.list',
                'admin.create',
                'admin.update',
                'admin.delete',
            ]);

            $time = microtime(true) - $start;
            $this->info($time);

            dd($res);
        } catch (\Exception $e){
            $this->error($e->getMessage(), []);
        }
    }

    protected function exec()
    {
        return app_languages();
    }
}
