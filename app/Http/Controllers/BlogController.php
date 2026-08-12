<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $posts = BlogPost::published()
            ->search($search)
            ->orderByDesc('published_at')
            ->paginate(9)
            ->withQueryString();

        $featured = null;
        if (empty($search) && $posts->currentPage() === 1 && $posts->isNotEmpty()) {
            $featured = $posts->first();
        }

        return view('blog.index', compact('posts', 'search', 'featured'));
    }

    public function show(string $slug): View
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Fetch up to 3 other recent published posts for recommendation
        $recentPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'recentPosts'));
    }
}
