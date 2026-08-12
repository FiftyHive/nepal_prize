@extends('layouts.app')

@section('title', 'Blog & Guides — Nepal Prize Checker')
@section('meta_description', 'Latest information, guidelines, and updates on the IRD Taxpayer Incentive Prize Program in Nepal.')

@section('content')
<div class="blog-container">

    {{-- Blog Page Header --}}
    <div class="blog-header">
        <div class="blog-header-badge">
            <span>📚 Guides &amp; Updates</span>
        </div>
        <h1>Taxpayer Incentive Insights</h1>
        <p>Stay updated with the latest rules, draw dates, claim processes, and announcements regarding Nepal's Taxpayer Incentive Prize Program.</p>

        {{-- Search Bar --}}
        <form method="GET" action="{{ route('blog.index') }}" class="search-bar-wrap">
            <span class="search-icon" aria-hidden="true">🔍</span>
            <input
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                placeholder="Search articles, draw updates, guides..."
                class="search-input"
                aria-label="Search blog posts"
            >
            @if(!empty($search))
                <a href="{{ route('blog.index') }}" style="position:absolute; right:1.2rem; top:50%; transform:translateY(-50%); color:#94a3b8; text-decoration:none; font-size:0.85rem; font-weight:700;">✕ Clear</a>
            @endif
        </form>
    </div>

    @if($posts->isEmpty())
        <div style="background:#ffffff; border-radius:var(--radius-lg); padding:3.5rem 2rem; text-align:center; box-shadow:var(--shadow-md); margin:2rem auto; max-width:540px;">
            <div style="font-size:2.5rem; margin-bottom:0.75rem;">🔍</div>
            <h2 style="font-size:1.25rem; font-weight:800; color:var(--text-main); margin-bottom:0.5rem;">
                @if(!empty($search))
                    No articles found for "{{ $search }}"
                @else
                    No articles published yet
                @endif
            </h2>
            <p style="color:var(--text-muted); font-size:0.92rem; margin-bottom:1.5rem;">
                @if(!empty($search))
                    Try searching with different keywords or browse all our articles.
                @else
                    We are currently preparing insightful guides and updates. Please check back soon!
                @endif
            </p>
            @if(!empty($search))
                <a href="{{ route('blog.index') }}" class="btn btn-brand" style="display:inline-flex; width:auto; padding:0.65rem 1.5rem;">
                    View All Articles
                </a>
            @else
                <a href="{{ route('home') }}" class="btn btn-brand" style="display:inline-flex; width:auto; padding:0.65rem 1.5rem;">
                    Back to Coupon Checker
                </a>
            @endif
        </div>
    @else
        {{-- Blog Grid --}}
        <div class="blog-grid">
            @foreach($posts as $post)
            <a href="{{ route('blog.show', $post->slug) }}" class="blog-card">
                <div class="blog-card-img-wrap">
                    @if($post->featured_image)
                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="blog-card-img" loading="lazy">
                    @else
                        <div class="blog-card-img-placeholder">
                            <span>📰</span>
                        </div>
                    @endif
                </div>

                <div class="blog-card-body">
                    <div class="blog-card-meta">
                        <span class="blog-pill">Guide</span>
                        <span>•</span>
                        <time datetime="{{ $post->published_at?->toIso8601String() }}">
                            {{ $post->published_at?->format('M d, Y') ?? 'Recently' }}
                        </time>
                        <span>•</span>
                        <span>{{ $post->reading_time }} min read</span>
                    </div>

                    <h2 class="blog-card-title">{{ $post->title }}</h2>

                    @if($post->excerpt)
                        <p class="blog-card-excerpt">{{ $post->excerpt }}</p>
                    @endif

                    <div class="blog-card-footer">
                        <span>Read full article</span>
                        <span class="blog-card-arrow" aria-hidden="true">→</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($posts->hasPages())
            <div style="display:flex; justify-content:center; margin-top:2rem;">
                {{ $posts->links() }}
            </div>
        @endif
    @endif

</div>
@endsection
