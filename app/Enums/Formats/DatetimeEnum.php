<?php

namespace App\Enums\Formats;

final class DatetimeEnum
{
    public const DEFAULT = 'datetime:' . self::DEFAULT_FORMAT;

    public const DEFAULT_FORMAT = 'Y-m-d H:i:s';

    public const DATE = 'Y-m-d';

    public const TIME = 'H:i:s';
    public const TIME_WITHOUT_SECONDS = 'H:i';

    public const CAST_TIME = 'date_format:' . self::TIME;

    public const AMERICAN_DATETIME_FORMAT = 'm-d-Y H:i:s';
    public const AMERICAN_DATETIME_FORMAT_VALIDATION_RULE = 'date_format:' . self::AMERICAN_DATETIME_FORMAT;
    public const AMERICAN_DATE_FORMAT_VALIDATION_RULE = 'date_format:' . self::AMERICAN_DATE_FORMAT;

    public const AMERICAN_DATE_FORMAT = 'm-d-Y';
}
