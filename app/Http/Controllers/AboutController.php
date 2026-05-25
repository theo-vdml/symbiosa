<?php

namespace App\Http\Controllers;

use App\Models\AboutPage;
use Inertia\Inertia;

class AboutController extends Controller
{
    public function __invoke()
    {
        $aboutPage = AboutPage::first();

        $sections = $aboutPage?->sections()
            ->get()
            ->map(fn($section) => [
                'title' => $section->title,
                'content' => $section->content,
                'image' => $section->getFirstMediaUrl('image'),
            ]) ?? [];

        return Inertia::render('About', [
            'sections' => $sections,
            'seo' => $aboutPage?->getSeoData() ?? [],
        ]);
    }
}
