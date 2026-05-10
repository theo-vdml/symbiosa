<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;

class NewsController extends Controller
{

    public function index()
    {
        $posts = Post::published()->latest('published_at')->get();
        $categories = $posts->pluck('category')->filter()->unique('id')->values();

        return Inertia::render('News/Index', [
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }

    public function show(string $slug)
    {
        $post = Post::published()->where('slug', $slug)->with('seo')->firstOrFail();

        return Inertia::render('News/Show', [
            'post' => $post,
            'seo' => [
                'title' => $post->seo?->title ?? $post->title,
                'description' => $post->seo?->description ?? $post->excerpt,
                'keywords' => implode(', ', $post->seo?->keywords ?? []),
                'robots' => $post->seo?->robots ?? 'index, follow',
                'canonical_url' => $post->seo?->canonical_url,
                'og_title' => $post->seo?->og_title ?? $post->seo?->title ?? $post->title,
                'og_description' => $post->seo?->og_description ?? $post->seo?->description ?? $post->excerpt,
                'og_image' => $post->seo?->og_image ?? $post->thumbnail,
                'og_type' => $post->seo?->og_type ?? 'article',
                'twitter_card' => $post->seo?->twitter_card ?? 'summary_large_image',
                'twitter_title' => $post->seo?->twitter_title ?? $post->seo?->title ?? $post->title,
                'twitter_description' => $post->seo?->twitter_description ?? $post->seo?->description ?? $post->excerpt,
                'twitter_image' => $post->seo?->twitter_image ?? $post->seo?->og_image ?? $post->thumbnail,
                'json_ld' => $post->seo?->json_ld ? json_encode($post->seo->json_ld) : null,
            ]
        ]);
    }
}
