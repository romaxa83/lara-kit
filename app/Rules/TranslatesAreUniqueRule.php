<?php

namespace App\Rules;

use App\Models\Package\FeaturesCategoryTranslates;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class TranslatesAreUniqueRule implements Rule
{
    public function __construct(protected ?int $id = null)
    {
    }

    public function passes($attribute, $value): bool
    {
        $builder = FeaturesCategoryTranslates::query()
            ->when(!empty($this->id), function (Builder $b) {
                $b->where('row_id', '!=', $this->id);
            })
            ->where(function (Builder $builder) use ($value) {
                foreach ($value as $item) {
                    $builder->orWhere(function (Builder $b) use ($item) {
                        $b->where('language', $item['language'])
                            ->where('name', $item['name']);
                    });
                }
            });

        return !$builder->exists();
    }

    public function message(): string
    {
        return __('validation.unique', ['attribute' => 'translate']);
    }
}
