<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_page_loads_and_displays_published_posts(): void
    {
        $post = BlogPost::create([
            'title'        => 'How to Check Your Prize Coupon Online',
            'slug'         => 'how-to-check-prize-coupon-online',
            'excerpt'      => 'A complete step-by-step guide to checking your taxpayer coupon.',
            'content'      => '<p>Step 1: Select your period. Step 2: Enter coupon number.</p>',
            'status'       => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/blog');
        $response->assertStatus(200);
        $response->assertSee('How to Check Your Prize Coupon Online');
    }

    public function test_blog_show_page_loads_published_article(): void
    {
        $post = BlogPost::create([
            'title'        => 'Guide to IRD Taxpayer Program',
            'slug'         => 'guide-to-ird-taxpayer-program',
            'excerpt'      => 'Learn all about the Government of Nepal incentive program.',
            'content'      => '<h2>Overview</h2><p>Here are the details of the program.</p>',
            'status'       => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get("/blog/{$post->slug}");
        $response->assertStatus(200);
        $response->assertSee('Guide to IRD Taxpayer Program');
        $response->assertSee('<h2>Overview</h2>', false);
    }

    public function test_draft_blog_post_returns_404(): void
    {
        $post = BlogPost::create([
            'title'        => 'Draft Article',
            'slug'         => 'draft-article-secret',
            'content'      => '<p>Secret content</p>',
            'status'       => 'draft',
            'published_at' => null,
        ]);

        $response = $this->get("/blog/{$post->slug}");
        $response->assertStatus(404);
    }

    public function test_blog_search_filters_articles(): void
    {
        BlogPost::create([
            'title'        => 'Unique Tax Incentive Guide 12345',
            'slug'         => 'unique-tax-incentive-guide-12345',
            'content'      => '<p>Content</p>',
            'status'       => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/blog?q=12345');
        $response->assertStatus(200);
        $response->assertSee('Unique Tax Incentive Guide 12345');
    }
}
