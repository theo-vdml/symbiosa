<?php

use App\Settings\PageSettingsMigration;

return new class extends PageSettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('about_page.sections', []);
        $this->addSeoSettings('about_page');
    }

    public function down(): void
    {
        $this->migrator->delete('about_page.sections');
        $this->deleteSeoSettings('about_page');
    }
};
