<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TokenValidationMiddlewareTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_can_not_access_api_with_missing_header_when_secret_token_configured()
    {
        // ARRANGE
        config(['fileproxy.api.secret_token' => 'S3cr3T']);

        // ACT
        $response = $this->getJson('/api/statistics');

        // ASSERT
        $response->assertStatus(401)
            ->assertExactJson([
                'errors' => [
                    [
                        'status' => 401,
                        'code' => 0,
                        'title' => 'Token missing'
                    ]
                ],
            ]);
    }

    /** @test */
    public function it_can_access_api_with_correct_header_when_secret_token_configured()
    {
        // ARRANGE
        config(['fileproxy.api.secret_token' => 'S3cr3T']);

        // ACT
        $response = $this->getJson('/api/statistics', ['X-FILEPROXY-TOKEN' => 'S3cr3T']);

        // ASSERT
        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.api+json')
            ->assertExactJson([
                'data' => [
                    'type' => 'statistics',
                    'id' => '',
                    'attributes' => [
                        'size' => 0,
                        'files' => 0,
                        'aliases' => 0,
                        'hits' => 0,
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/statistics/',
                    ],
                ]
            ]);
    }

    /** @test */
    public function it_can_access_api_with_correct_header_when_secret_token_configured_and_token_name_header_modified()
    {
        // ARRANGE
        config(['fileproxy.api.secret_token' => 'S3cr3T', 'fileproxy.api.token_name' => 'X-TEST']);

        // ACT
        $response = $this->getJson('/api/statistics', ['X-TEST' => 'S3cr3T']);

        // ASSERT
        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.api+json')
            ->assertExactJson([
                'data' => [
                    'type' => 'statistics',
                    'id' => '',
                    'attributes' => [
                        'size' => 0,
                        'files' => 0,
                        'aliases' => 0,
                        'hits' => 0,
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/statistics/',
                    ],
                ]
            ]);
    }

    /** @test */
    public function it_can_access_health_check_with_secured_api_without_secret_token()
    {
        // ARRANGE
        config(['fileproxy.api.secret_token' => 'S3cr3T']);

        // ACT
        $response = $this->get('/api/health');

        // ASSERT
        $response->assertStatus(200);
    }

}
