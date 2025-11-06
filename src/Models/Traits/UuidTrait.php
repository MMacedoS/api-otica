<?php

namespace App\Models\Traits;

trait UuidTrait
{
    public function generateUuid(): string
    {
        return bin2hex(random_bytes(16));
    }
}
