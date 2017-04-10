<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StatisticsResourceTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_can_fetch_the_statistics_resource()
    {
        // ARRANGE

        // ACT
        $response = $this->getJson('/api/statistics');

        // ASSERT
        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.api+json')
            ->assertExactJson([
                'data' => [
                    'type' => 'statistics',
                    'id' => 'statistics',
                    'attributes' => [
                        'size' => 0,
                        'files' => 0,
                        'aliases' => 0,
                        'hits' => 0,
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/statistics/statistics',
                    ],
                ]
            ]);
    }

}
