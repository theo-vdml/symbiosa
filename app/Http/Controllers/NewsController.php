<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;

class NewsController extends Controller
{

    public function index()
    {
        $posts = Post::published()->latest('published_at')->get();
        $categories = $posts->pluck('category')->flatten()->unique('id')->values();

        return Inertia::render('News/Index', [
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }

    public function show(string $slug)
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        return Inertia::render('News/Show', [
            'post' => $post,
        ]);
    }
}
