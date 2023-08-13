<?php

namespace App\Providers;

use App\Modules\Admin\Models\Admin;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->registerMorphMap();
    }

    protected function registerMorphMap(): void
    {
        Relation::morphMap(self::morphs());
    }

    public static function morphs(): array
    {
        return [
            Admin::MORPH_NAME => Admin::class,
        ];
    }
}
