<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class HealthResourceTest extends TestCase
{
    /** @test */
    public function it_can_access_the_health_check_resource()
    {
        // ARRANGE

    	// ACT
        $response = $this->get('/api/health');

    	// ASSERT
    	$response->assertStatus(200);
    }

}
