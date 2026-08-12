@extends('layouts.app')

@section('title', $post->effective_seo_title)
@section('meta_description', $post->effective_seo_description)
@section('canonical', route('blog.show', $post->slug))

@push('styles')
<meta property="og:title" content="{{ $post->effective_seo_title }}">
<meta property="og:description" content="{{ $post->effective_seo_description }}">
<meta property="og:type" content="article">
<meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}">
@if($post->featured_image)
<meta property="og:image" content="{{ asset('storage/' . $post->featured_image) }}">
<meta name="twitter:image" content="{{ asset('storage/' . $post->featured_image) }}">
@endif
@endpush

@section('content')
<div class="article-wrap">

    {{-- Back Nav --}}
    <div class="article-back-nav">
        <a href="{{ route('blog.index') }}" class="article-back-btn">
            <span aria-hidden="true">←</span> Back to All Articles
        </a>
    </div>

    {{-- Main Article Card --}}
    <article class="article-card">
        <header class="article-header">
            <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.75rem;">
                <span class="blog-pill">Guide &amp; News</span>
                <span style="color:#cbd5e1;">•</span>
                <span style="font-size:0.85rem; color:#64748b; font-weight:600;">{{ $post->reading_time }} min read</span>
            </div>

            <h1 class="article-title">{{ $post->title }}</h1>

            <div class="article-meta-row">
                <div class="article-meta-details">
                    <span style="font-weight:600; color:var(--text-main);">Published:</span>
                    <time datetime="{{ $post->published_at?->toIso8601String() }}">
                        {{ $post->published_at?->format('F d, Y') ?? 'Recently' }}
                    </time>
                </div>
            </div>
        </header>

        @if($post->featured_image)
            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="article-featured-image">
        @endif

        {{-- Main Article Content (Render rich HTML from editor) --}}
        <div class="article-content">
            {!! $post->content !!}
        </div>

        {{-- Share Bar --}}
        <div class="share-bar">
            <span class="share-label">Share article:</span>

            {{-- Copy Link Button --}}
            <button type="button" class="share-btn" onclick="copyArticleLink()" id="copy-btn">
                <span>🔗</span> <span id="copy-text">Copy Link</span>
            </button>

            {{-- WhatsApp Share --}}
            <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . route('blog.show', $post->slug)) }}" target="_blank" rel="noopener noreferrer" class="share-btn">
                <span>💬</span> WhatsApp
            </a>

            {{-- Facebook Share --}}
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post->slug)) }}" target="_blank" rel="noopener noreferrer" class="share-btn">
                <span>📘</span> Facebook
            </a>

            {{-- Twitter/X Share --}}
            <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(route('blog.show', $post->slug)) }}" target="_blank" rel="noopener noreferrer" class="share-btn">
                <span>🐦</span> X / Twitter
            </a>
        </div>

        {{-- Coupon Checker Callout Banner --}}
        <div class="article-cta-box">
            <div>
                <h3>Have you checked your coupons?</h3>
                <p>Verify whether your IRD taxpayer incentive prize coupon has been allotted a winning draw.</p>
            </div>
            <a href="{{ route('home') }}" class="article-cta-btn">
                Check My Coupon →
            </a>
        </div>
    </article>

    {{-- Recent / Related Articles Section --}}
    @if(isset($recentPosts) && $recentPosts->isNotEmpty())
        <div style="margin-top:3.5rem;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem;">
                <h2 style="font-size:1.4rem; font-weight:800; color:#ffffff; letter-spacing:-0.02em;">Other Recent Articles</h2>
                <a href="{{ route('blog.index') }}" style="color:#fb7185; font-size:0.88rem; font-weight:700; text-decoration:none;">View all →</a>
            </div>

            <div class="blog-grid" style="margin-bottom:0;">
                @foreach($recentPosts as $recent)
                    <a href="{{ route('blog.show', $recent->slug) }}" class="blog-card">
                        <div class="blog-card-img-wrap" style="height:150px;">
                            @if($recent->featured_image)
                                <img src="{{ asset('storage/' . $recent->featured_image) }}" alt="{{ $recent->title }}" class="blog-card-img" loading="lazy">
                            @else
                                <div class="blog-card-img-placeholder" style="font-size:1.8rem;">
                                    <span>📰</span>
                                </div>
                            @endif
                        </div>
                        <div class="blog-card-body" style="padding:1.2rem;">
                            <div class="blog-card-meta" style="font-size:0.75rem; margin-bottom:0.4rem;">
                                <span>{{ $recent->published_at?->format('M d, Y') ?? 'Recently' }}</span>
                                <span>•</span>
                                <span>{{ $recent->reading_time }} min read</span>
                            </div>
                            <h3 class="blog-card-title" style="font-size:1.05rem; margin-bottom:0.4rem;">{{ $recent->title }}</h3>
                            <div class="blog-card-footer" style="padding-top:0.6rem; font-size:0.8rem;">
                                <span>Read article</span>
                                <span class="blog-card-arrow" aria-hidden="true">→</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function copyArticleLink() {
    const url = window.location.href;
    const btnText = document.getElementById('copy-text');
    navigator.clipboard.writeText(url).then(function() {
        btnText.textContent = 'Copied!';
        setTimeout(function() {
            btnText.textContent = 'Copy Link';
        }, 2000);
    }).catch(function() {
        btnText.textContent = 'Copied!';
    });
}
</script>
@endpush
