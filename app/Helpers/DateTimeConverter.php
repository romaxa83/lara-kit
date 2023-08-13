<?php

namespace App\Helpers;

use App\Enums\Formats\DatetimeEnum;
use App\Models\Timezoneable;
use Carbon\Carbon;

class DateTimeConverter
{
    public static function prepareDatetimeForDB(?string $datetime, string $fromTimezone): ?string
    {
        if (empty($datetime)) {
            return null;
        }

        $datetime = Carbon::createFromFormat(
            DatetimeEnum::AMERICAN_DATETIME_FORMAT,
            $datetime,
            $fromTimezone ?? config('app.timezone')
        );

        $datetime->setTimezone(config('app.timezone'));

        return $datetime->format(DatetimeEnum::DEFAULT_FORMAT);
    }

    public static function prepareDateForDB(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        $date = Carbon::createFromFormat(
            DatetimeEnum::AMERICAN_DATE_FORMAT,
            $date
        );

        return $date->format(DatetimeEnum::DATE);
    }

    public static function prepareDatetimeForDBNoTimezone(?string $datetime, string|null $fromTimezone = null): ?string
    {
        if (empty($datetime)) {
            return null;
        }

        $datetime = Carbon::createFromFormat(
            DatetimeEnum::AMERICAN_DATETIME_FORMAT,
            $datetime
        );

        $datetime->setTimezone(config('app.timezone'));

        return $datetime->format(DatetimeEnum::DEFAULT_FORMAT);
    }

    public static function prepareDatetimeForClient(?Carbon $datetime, mixed $user): Carbon|string|null
    {
        if ($user instanceof Timezoneable) {
            return $datetime
                ?->setTimezone($user->getTimezone())
                ?->format(DatetimeEnum::AMERICAN_DATETIME_FORMAT);
        }

        return $datetime;
    }

    public static function prepareDateForClient(?Carbon $datetime, mixed $user): Carbon|string|null
    {
        if ($user instanceof Timezoneable) {
            return $datetime
//                ?->setTimezone($user->getTimezone())
                ?->format(DatetimeEnum::AMERICAN_DATE_FORMAT);
        }

        return $datetime;
    }
}
