<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_home(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/home');
    }

    public function test_home_page_returns_successful_response(): void
    {
        $response = $this->get('/home');
        $response->assertStatus(200);
    }
}
