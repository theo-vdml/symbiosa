<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('asbl.name', 'Symbiosa ASBL');
        $this->migrator->add('asbl.address', '');
        $this->migrator->add('asbl.vat', '');
    }
};
