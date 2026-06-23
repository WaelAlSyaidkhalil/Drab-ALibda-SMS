<?php

namespace App\Services\Admin;

use Illuminate\Support\Str; 

class GeneratePasswordService
{
    public static function generatePassword(): string
    {
        return 'Darb_' . Str::password(4, symbols: false);
    }
}