@extends('layouts.app')

@section('title', $post->effective_seo_title)
@section('meta_description', $post->effective_seo_description)
@section('canonical', route('blog.show', $post->slug))

@push('styles')
<meta property="og:title" content="{{ $post->effective_seo_title }}">
<meta property="og:description" content="{{ $post->effective_seo_description }}">
@if($post->featured_image)
<meta property="og:image" content="{{ asset('storage/' . $post->featured_image) }}">
@endif
@endpush

@section('content')
<div class="white-card">
    {{-- Mini logo --}}
    <div style="text-align:center; margin-bottom:1.5rem;">
        <a href="{{ route('home') }}" class="logo" style="text-decoration:none;">
            <span style="font-size:1.1rem; font-weight:900; letter-spacing:-.025em; color:#111827;">
                Nepal<span style="color:#dc2626;">Prize</span>Checker
            </span>
        </a>
    </div>

    <div style="margin-bottom:.85rem;">
        <a href="{{ route('blog.index') }}" style="font-size:.83rem; color:#6b7280; text-decoration:none;">← Back to Blog</a>
    </div>

    <article>
        <header style="margin-bottom:1.25rem;">
            <h1 class="article-title">{{ $post->title }}</h1>
            <div class="article-meta">Published {{ $post->published_at?->format('d F Y') }}</div>
        </header>

        @if($post->featured_image)
        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="article-featured-img">
        @endif

        <div class="article-content">
            {!! nl2br(e($post->content)) !!}
        </div>
    </article>
</div>
@endsection
