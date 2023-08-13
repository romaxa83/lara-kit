<?php

namespace App\Traits;

use Illuminate\Support\Facades\Hash;

trait SetPasswordTrait
{
    public function setPassword(string $password, bool $save = false): self
    {
        $this->setAttribute('password', Hash::make($password));
        if($save){
            $this->save();
        }

        return $this;
    }
}
