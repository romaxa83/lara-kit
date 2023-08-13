<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if(!App::environment('testing')){
            $this->call([
                LanguageDefaultSeeder::class,
                PermissionsSeeder::class,
                RolesSeeder::class,
                SuperAdminSeeder::class
            ]);
        }

        // for local
        if(App::environment('local')){
            $this->call([]);
        }

        if(!App::environment('testing')){
            foreach (glob(base_path('app/Console/Commands/FixDB'). "/*.php") as $filename) {
                $name = substr(last(explode('/', $filename)),0, -4);
                Artisan::call('App\Console\Commands\FixDB\\' . $name);
            }
        }
    }
}
