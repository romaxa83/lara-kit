<?php

namespace App\Entities\Auth;

class EmailTokenEntity
{
    public int $id;
    public int $time;
    public int $code;
    public string $guard;

    public function __construct(array $encrypted)
    {
        $this->id = $encrypted['id'];
        $this->time = $encrypted['time'];
        $this->code = $encrypted['code'];
        $this->guard = $encrypted['guard'];
    }
}

