<?php

namespace App\Dto;

abstract class BaseMultilangDto
{
    /**
     * @var TranslateDtoInterface[]
     */
    protected array $translates;

    protected function generateTranslatesDto(array $translates) :array
    {
        $translatesDto = [];
        foreach ($translates as $translate) {
            $translatesDto[] = $this->getTranslationDtoClass()->fillTranslates($translate);
        }

        return $translatesDto;
    }

    abstract protected function getTranslationDtoClass(): TranslateDtoInterface;

    /**
     * @return TranslateDtoInterface[]
     */
    public function getTranslates(): array
    {
        return $this->translates;
    }
}
