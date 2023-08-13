<?php

namespace App\GraphQL\Middlewares\Localization;

use App\Models\Admins\Admin;
use App\Modules\Localization\Contracts\Languageable;
use App\Modules\Permissions\Enums\Guard;
use Closure;
use Core\Models\BaseAuthenticatable;
use Core\Traits\Auth\AuthGuardsTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

class SystemLangSetterMiddleware
{
    use AuthGuardsTrait;

    public function handle(Request $request, Closure $next)
    {
        if (
            ($lang = $request->headers->get(config('localization.header')))
            && app('localization')->hasLang($lang)
        ) {
            $this->setLocale($request, $lang);

            return $next($request);
        }

        if (($auth = $this->getCurrentAuth()) && ($lang = $auth->getLangSlug())) {
            $this->setLocale($request, $lang);
        }

        return $next($request);
    }

    protected function setLocale(Request $request, string $lang): void
    {
        Lang::setLocale($lang);

        Config::set('app.locale', $lang);

        $request->headers->set('Language', $lang);
    }

    protected function getCurrentAuth(): BaseAuthenticatable|Authenticatable|Languageable|null
    {
        if ($this->authCheck(Guard::ADMIN)) {
            return $this->user(Guard::ADMIN);
        }

        if ($this->authCheck(Guard::USER)) {
            return $this->user(Guard::USER);
        }

        return null;
    }
}
