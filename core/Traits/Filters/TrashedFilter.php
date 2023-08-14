<?php

namespace Core\Traits\Filters;

trait TrashedFilter
{
    public function withTrash(bool $value): void
    {
        if($value){
            $this->withTrashed();
        }
    }

    public function onlyTrash(bool $value): void
    {
        if($value) {
            $this->onlyTrashed();
        }
    }
}
