<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AsblSettings extends Settings
{
    public string $name;
    public string $address;
    public ?string $vat;

    public static function group(): string
    {
        return 'asbl';
    }
}
