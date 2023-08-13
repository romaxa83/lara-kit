<?php

namespace App\Models;

interface Timezoneable
{
    public function getTimezone(): string;

    public function setTimezone(string $timezone): self;
}
