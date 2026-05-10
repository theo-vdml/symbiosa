<?php

namespace App\Http\Controllers;

use App\Settings\AboutPageSettings;
use Inertia\Inertia;

class AboutController extends Controller
{
    public function __invoke(AboutPageSettings $settings)
    {
        return Inertia::render('About', [
            'sections' => $settings->sections ?? [],
            'seo' => [
                'title' => $settings->seo_title,
                'description' => $settings->seo_description,
                'keywords' => implode(', ', $settings->seo_keywords ?? []),
                'robots' => $settings->seo_robots,
                'canonical_url' => $settings->seo_canonical_url,
                'og_title' => $settings->seo_og_title,
                'og_description' => $settings->seo_og_description,
                'og_image' => $settings->seo_og_image,
                'og_type' => $settings->seo_og_type,
                'twitter_card' => $settings->seo_twitter_card,
                'twitter_title' => $settings->seo_twitter_title,
                'twitter_description' => $settings->seo_twitter_description,
                'twitter_image' => $settings->seo_twitter_image,
                'json_ld' => $settings->seo_json_ld ? json_encode($settings->seo_json_ld) : null,
            ]
        ]);
    }
}
