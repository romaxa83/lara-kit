<?php

use App\Helpers\DateTimeConverter;
use App\Models\Localization\Locale;
use App\Modules\Localization\Models\Language;
use App\Modules\Localization\Repositories\LanguageRepository;
use Carbon\Carbon;
use Core\Models\BaseModel;
use Core\Services\Cache\LockerService;
use Core\Services\Database\TransactionService;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use JetBrains\PhpStorm\Pure;

if (!function_exists('logger_info')) {

    function logger_info($message, array $context = [], bool $important = true)
    {
        if(config('logging.channels.eyes.enable') || $important){
            Illuminate\Support\Facades\Log::channel('eyes')->info($message, $context);
        }
    }
}

if (!function_exists('date_interval_to_seconds')) {
    function date_interval_to_seconds(DateInterval $interval): int
    {
        return $interval->days * 86400 + $interval->h * 3600
            + $interval->i * 60 + $interval->s + round($interval->f);
    }
}

if (!function_exists('json_to_array')) {
    function json_to_array(?string $jsonString = ''): array
    {
        return json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR) ?: [];
    }
}

if (!function_exists('array_to_json')) {
    function array_to_json(array $array, $options = 0): string
    {
        return json_encode($array, JSON_THROW_ON_ERROR | $options);
    }
}

if (!function_exists('std_to_array')) {
    function std_to_array(object $std): array
    {
        return json_decode(json_encode($std), true);
    }
}

if (!function_exists('is_testing')) {
    #[Pure] function is_testing(): bool
    {
        return config('app.env') === 'testing';
    }
}

if (!function_exists('is_prod')) {
    #[Pure] function is_prod(): bool
    {
        return config('app.env') === 'production';
    }
}

if (!function_exists('languages')) {
    /**
     * @return Collection|Language[]
     */
    function languages(): Collection|array
    {
        return app('localization')->getAllLanguages();
    }
}

if (!function_exists('locales')) {
    /**
     * @return Collection<Locale>
     */
    function locales(): Collection
    {
        return app('locales')->getAllLocales();
    }
}

if (!function_exists('default_language')) {
    function default_language(): Language
    {
        return app('localization')->getDefault();
    }
}

if (!function_exists('yes_or_no')) {
    function yes_or_no(mixed $value): string
    {
        return (bool)$value ? 'yes' : 'no';
    }
}

if (!function_exists('trim_ds')) {
    function trim_ds(string $value): string
    {
        return trim($value, DIRECTORY_SEPARATOR);
    }
}

if (!function_exists('locker')) {
    function locker(): LockerService
    {
        return app(LockerService::class);
    }
}

if (!function_exists('to_model_key')) {
    function to_model_key(BaseModel|int|string $model): int|string
    {
        return $model instanceof BaseModel
            ? $model->getKey()
            : $model;
    }
}

if (!function_exists('make_transaction')) {
    /**
     * @param  Closure  $action
     * @param  array<Connection>  $connections
     * @return mixed
     * @throws Throwable
     */
    function make_transaction(Closure $action, array $connections = []): mixed
    {
        return app(TransactionService::class)->handle($action, $connections);
    }
}

if (!function_exists('get_current_running_time')) {
    function get_current_running_time(): float
    {
        return (microtime(true) - LARAVEL_START);
    }
}

if (!function_exists('builder_to_sql')) {
    /**
     * @param  \Illuminate\Database\Eloquent\Builder|Builder  $builder
     * @return string
     */
    function builder_to_sql($builder): string
    {
        $sql = str_replace('?', '%s', $builder->toSql());

        return vsprintf($sql, $builder->getBindings());
    }
}

if (!function_exists('prepare_date_time_for_client')) {
    function prepare_date_time_for_client(?Carbon $datetime, mixed $user): ?string
    {
        return DateTimeConverter::prepareDatetimeForClient($datetime, $user);
    }
}

if (!function_exists('prepare_date_for_client')) {
    function prepare_date_for_client(?Carbon $datetime, mixed $user): ?string
    {
        return DateTimeConverter::prepareDateForClient($datetime, $user);
    }
}

if (!function_exists('prepare_datetime_for_db')) {
    function prepare_datetime_for_db(?string $datetime, string $fromTimezone): ?string
    {
        return DateTimeConverter::prepareDatetimeForDB($datetime, $fromTimezone);
    }
}

if (!function_exists('prepare_date_for_db')) {
    function prepare_date_for_db(?string $date): ?string
    {
        return DateTimeConverter::prepareDateForDB($date);
    }
}

if (!function_exists('prepare_datetime_for_db_no_timezone')) {
    function prepare_datetime_for_db_no_timezone(?string $datetime, string|null $fromTimezone = null): ?string
    {
        return DateTimeConverter::prepareDatetimeForDBNoTimezone($datetime, $fromTimezone);
    }
}

if (!function_exists('remove_underscore')) {

    function remove_underscore(string $str): string
    {
        return str_replace('_', ' ', $str);
    }
}

if (!function_exists('app_languages')) {

    function app_languages(string $key = 'slug', string $value = 'name'): array
    {
        return resolve(LanguageRepository::class)->getLanguagesAsArray($key, $value);
    }
}

if (!function_exists('is_support_lang')) {

    function is_support_lang(string $lang): bool
    {
        return array_key_exists($lang, app_languages());
    }
}

if (!function_exists('support_langs_as_str')) {

    function support_langs_as_str(): string
    {
        return implode(', ', app_languages(value: 'slug'));
    }
}

if (!function_exists('default_lang')) {

    function default_lang(): Language
    {
        $model = resolve(LanguageRepository::class)->getDefault();

        if(!$model){
            throw new \App\Modules\Localization\Exceptions\LocalizationException(
                __('exceptions.localization.default_language_not_set')
            );
        }

        return $model;
    }
}

if (!function_exists('phone_clear')) {

    function phone_clear(string $phone): string
    {
        return str_replace(['+', '-', ' ', '.', '(', ')'], '', $phone);
    }
}

if (!function_exists('is_contains')) {

    function is_contains($value, string $key = null): bool
    {
        if(is_array($value)){
            if (!$key){
                throw new \Exception('function "is_contains" must have a key');
            }

            return isset($value[$key]) && !empty($value[$key]);
        }

        return isset($value) && !empty($value);
    }
}

if (!function_exists('hash_data')) {

    function hash_data(array|string|int $data): string
    {
        if(is_array($data)){
            $data = json_encode($data);
        }

        return md5($data);
    }
}
