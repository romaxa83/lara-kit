<?php

namespace App\Modules\Admin\Events;

use App\Modules\Admin\Dto\AdminDto;
use App\Modules\Admin\Models\Admin;

class AdminCreatedEvent
{
    public function __construct(
        protected Admin $model,
        protected ?AdminDto $dto = null,
    )
    {}

    public function getModel(): Admin
    {
        return $this->model;
    }

    public function getDto(): ?AdminDto
    {
        return $this->dto;
    }
}
