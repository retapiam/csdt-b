<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutesSmokeTest extends TestCase
{
    /** @test */
    public function health_check_responde_ok()
    {
        $response = $this->get('/api/health');
        $response->assertStatus(200);
    }
}


