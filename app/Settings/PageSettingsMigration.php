<?php

namespace App\Settings;

use Spatie\LaravelSettings\Migrations\SettingsMigration;

abstract class PageSettingsMigration extends SettingsMigration
{
    protected function addSeoSettings(string $group): void
    {
        $this->migrator->add("{$group}.seo_title", null);
        $this->migrator->add("{$group}.seo_description", null);
        $this->migrator->add("{$group}.seo_keywords", []);
        $this->migrator->add("{$group}.seo_robots", 'index, follow');
        $this->migrator->add("{$group}.seo_canonical_url", null);
        $this->migrator->add("{$group}.seo_og_title", null);
        $this->migrator->add("{$group}.seo_og_description", null);
        $this->migrator->add("{$group}.seo_og_image", null);
        $this->migrator->add("{$group}.seo_og_type", 'website');
        $this->migrator->add("{$group}.seo_twitter_card", 'summary_large_image');
        $this->migrator->add("{$group}.seo_twitter_title", null);
        $this->migrator->add("{$group}.seo_twitter_description", null);
        $this->migrator->add("{$group}.seo_twitter_image", null);
        $this->migrator->add("{$group}.seo_json_ld", null);
    }

    protected function deleteSeoSettings(string $group): void
    {
        $this->migrator->delete("{$group}.seo_title");
        $this->migrator->delete("{$group}.seo_description");
        $this->migrator->delete("{$group}.seo_keywords");
        $this->migrator->delete("{$group}.seo_robots");
        $this->migrator->delete("{$group}.seo_canonical_url");
        $this->migrator->delete("{$group}.seo_og_title");
        $this->migrator->delete("{$group}.seo_og_description");
        $this->migrator->delete("{$group}.seo_og_image");
        $this->migrator->delete("{$group}.seo_og_type");
        $this->migrator->delete("{$group}.seo_twitter_card");
        $this->migrator->delete("{$group}.seo_twitter_title");
        $this->migrator->delete("{$group}.seo_twitter_description");
        $this->migrator->delete("{$group}.seo_twitter_image");
        $this->migrator->delete("{$group}.seo_json_ld");
    }
}
