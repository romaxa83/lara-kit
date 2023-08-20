<?php

namespace Tests\Builders;

use App\Modules\Utils\Phones\Models\Phone;
use Core\Models\BaseTranslation;
use Faker\Generator;

abstract class BaseBuilder
{
    protected array $data = [];
    protected bool $withTranslation = false;
    protected array $translationData = [];

    protected Generator $faker;

    public function __construct()
    {
        $this->faker = resolve(Generator::class);
    }

    abstract protected function modelClass(): string;

    protected function getModelTranslationClass(): string
    {
        return '';
    }

    public function setData(array $data): self
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    public function withTranslation(array $data = []): self
    {
        $this->withTranslation = true;
        $this->translationData = $data;

        return $this;
    }

    public function create()
    {
        $this->beforeSave();

        $model = $this->save();

        if ($this->withTranslation) {
            $this->createTranslation($model->id);
        }

        $this->afterSave($model);

        $this->clear();
        $this->afterClear();

        return $model;
    }

    protected function save()
    {
        if($this->modelClass() instanceof Phone){

        dd($this->data, $this->modelClass());
        }
        return $this->modelClass()::factory()->create($this->data);
    }

    protected function beforeSave(): void
    {}

    protected function afterSave($model): void
    {}

    protected function afterClear(): void
    {}

    private function createTranslation($id): void
    {
        /** @var $class BaseTranslation */
        $class = $this->getModelTranslationClass();

        $langs = app_languages();

        foreach ($langs as $lang => $name){
            $class::factory(array_merge(
                [
                    'row_id' => $id,
                    'lang' => $lang,
                ],
                data_get($this->translationData, $lang, [])
            ))->create();
        }
    }

    protected function clear(): void
    {
        $this->data = [];
        $this->translationData = [];
        $this->withTranslation = false;
    }
}

