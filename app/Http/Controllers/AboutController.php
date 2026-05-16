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
            'seo' => $settings->getSeoData(),
        ]);
    }
}
