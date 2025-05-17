<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_github_webhook_handling()
    {
        $payload = [
            'commits' => [
                [
                    'id' => 'abc123',
                    'message' => 'Added a new feature',
                    'author' => ['name' => 'Rudra'],
                ]
            ]
        ];

        $response = $this->postJson('/api/webhook', $payload, [
            'X-GitHub-Event' => 'push',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('git_hub_commits', [
            'commit_id' => 'abc123',
            'author' => 'Rudra',
        ]);
    }

    public function test_stripe_webhook_handling()
    {
        $payload = [
            'data' => [
                'object' => [
                    'amount' => 100,
                    'currency' => 'usd',
                    'status' => 'succeeded',
                ]
            ]
        ];

        $response = $this->postJson('/api/webhook', $payload, [
            'Stripe-Signature' => 'dummy-signature',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('stripe_transactions', [
            'amount' => 100,
            'currency' => 'usd',
            'payment_status' => 'succeeded',
        ]);
    }

    public function test_custom_webhook_handling()
    {
        $payload = [
            'event' => 'custom_event',
            'details' => ['info' => 'Test']
        ];

        $response = $this->postJson('/api/webhook', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseCount('custom_webhook_logs', 1);
    }

    // public function test_invalid_json_returns_error()
    // {
    //     $response = $this->postJson('/api/webhook', 'INVALID JSON', [
    //         'Content-Type' => 'application/json',
    //     ]);

    //     $response->assertStatus(400);
    // }
}
