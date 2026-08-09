@extends('layouts.app')

@section('title', 'Blog — Nepal Prize Checker')
@section('meta_description', 'Latest information and guides about the IRD taxpayer incentive prize program.')

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

    <h1 class="page-title">Latest Information</h1>

    @if($posts->isEmpty())
        <p style="color:#6b7280; font-size:.9rem;">No posts published yet. Check back soon.</p>
    @else
        <ul class="post-list">
            @foreach($posts as $post)
            <li class="post-item">
                <div class="post-title">
                    <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                </div>
                <div class="post-meta">{{ $post->published_at?->format('d M Y') }}</div>
                @if($post->excerpt)
                    <p class="post-excerpt">{{ $post->excerpt }}</p>
                @endif
                <a href="{{ route('blog.show', $post->slug) }}" class="read-more">Read more →</a>
            </li>
            @endforeach
        </ul>
        <div style="margin-top:1.5rem;">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
