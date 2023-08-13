<?php

namespace Core\Traits\GraphQL\Queries;

use App\Enums\Formats\DatetimeEnum;
use App\GraphQL\Types\NonNullType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;

trait BetweenDateRangeTrait
{
    protected string $dateFrom = 'date_from';
    protected string $dateTo = 'date_to';

    protected bool $dateFromRequired = true;
    protected bool $dateToRequired = true;
    protected bool $dateToSequenceValidation = true;

    public function betweenDateRangeArgs(): array
    {
        return [
            $this->dateFrom => [
                'type' => $this->getTypeByRequiredValue($this->dateFromRequired),
                'description' => 'Формат даты: ' . DatetimeEnum::DEFAULT_FORMAT,
            ],
            $this->dateTo => [
                'type' => $this->getTypeByRequiredValue($this->dateToRequired),
                'description' => 'Формат даты: ' . DatetimeEnum::DEFAULT_FORMAT,
            ],
        ];
    }

    protected function getTypeByRequiredValue(bool $required = true): ScalarType|Type
    {
        return $required
            ? NonNullType::string()
            : Type::string();
    }

    public function betweenDateRangeRules(): array
    {
        $datetimeFormat = 'date_format:' . DatetimeEnum::DEFAULT_FORMAT;

        $rules = [
            $this->dateFrom => [
                $this->dateFromRequired ? 'required' : 'nullable',
                $datetimeFormat
            ],
            $this->dateTo => [
                $this->dateToRequired ? 'required' : 'nullable',
                $datetimeFormat,
            ]
        ];

        if ($this->dateToSequenceValidation) {
            $rules[$this->dateTo][] = 'after:' . $this->dateFrom;
        }

        return $rules;
    }
}
