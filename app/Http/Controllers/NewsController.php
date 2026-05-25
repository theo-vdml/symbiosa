<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;

class NewsController extends Controller
{

    public function index()
    {
        $page = \App\Models\NewsPage::first() ?? new \App\Models\NewsPage();
        $posts = Post::published()->latest('published_at')->get();
        $categories = $posts->pluck('category')->filter()->unique('id')->values();

        return Inertia::render('News/Index', [
            'preheading' => $page->preheading ?? "Actualités",
            'heading' => $page->heading ?? 'Nos Actualités',
            'description' => $page->description ?? '',
            'posts' => $posts,
            'categories' => $categories,
            'seo' => $page->getSeoData(),
        ]);
    }

    public function show(string $slug)
    {
        $post = Post::published()->where('slug', $slug)->with('seo')->firstOrFail();

        return Inertia::render('News/Show', [
            'post' => $post,
            'seo' => $post->getSeoData(),
        ]);
    }
}
