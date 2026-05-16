<?php

use App\Settings\PageSettingsMigration;

return new class extends PageSettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('homepage.spotify_playlist_heading', null);
        $this->migrator->add('homepage.spotify_playlist_id', null);
        $this->migrator->add('homepage.show_spotify_playlist', false);
        $this->addSeoSettings('homepage');
    }

    public function down(): void
    {
        $this->migrator->delete('homepage.spotify_playlist_heading');
        $this->migrator->delete('homepage.spotify_playlist_id');
        $this->migrator->delete('homepage.show_spotify_playlist');
        $this->deleteSeoSettings('homepage');
    }
};
