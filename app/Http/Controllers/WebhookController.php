<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class WebhookController
 *
 * Handles incoming webhooks from various sources including GitHub, Stripe, and custom webhooks.
 * Processes and stores webhook data in appropriate database tables.
 */
class WebhookController extends Controller
{
    /**
     * Handle incoming webhook requests
     *
     * @param Request $request The incoming HTTP request
     * @return \Illuminate\Http\JsonResponse Returns JSON response indicating success or failure
     *
     * @throws \Exception When webhook processing fails
     */
    public function handle(Request $request)
    {
        try {
            $payload = $request->getContent();
            $json = json_decode($payload, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['error' => 'Invalid JSON'], 400);
            }

            Log::info('Incoming Webhook:', $json);

            $source = $this->identifySource($request, $json);

            switch ($source) {
                case 'github':
                    $this->handleGitHub($json);
                    break;
                case 'stripe':
                    $this->handleStripe($json);
                    break;
                default:
                    $this->handleCustom($json);
                    break;
            }

            return response()->json(['message' => 'Webhook processed'], 200);
        } catch (\Exception $e) {
            Log::error('Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Identify the source of the webhook based on request headers
     *
     * @param Request $request The incoming HTTP request
     * @param array $json The decoded JSON payload
     * @return string Returns 'github', 'stripe', or 'custom' based on the webhook source
     */
    protected function identifySource(Request $request, array $json): string
    {
        if ($request->hasHeader('X-GitHub-Event')) {
            return 'github';
        }

        if ($request->hasHeader('Stripe-Signature')) {
            return 'stripe';
        }

        return 'custom';
    }

    /**
     * Process GitHub webhook events
     *
     * Stores commit information including commit ID, message, and author
     * into the GitHubCommit model.
     *
     * @param array $json The GitHub webhook payload
     * @return void
     */
    protected function handleGitHub(array $json)
    {
        if (!isset($json['commits'])) return;

        foreach ($json['commits'] as $commit) {
            \App\Models\GitHubCommit::create([
                'commit_id' => $commit['id'],
                'message' => $commit['message'],
                'author' => $commit['author']['name'],
            ]);
        }
    }

    /**
     * Process Stripe webhook events
     *
     * Stores transaction information including amount, currency, and payment status
     * into the StripeTransaction model.
     *
     * @param array $json The Stripe webhook payload
     * @return void
     */
    protected function handleStripe(array $json)
    {
        $data = $json['data']['object'] ?? null;
        if (!$data) return;

        \App\Models\StripeTransaction::create([
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'payment_status' => $data['status'],
        ]);
    }

    /**
     * Process custom webhook events
     *
     * Stores the entire payload in the CustomWebhookLog model for
     * custom webhook processing.
     *
     * @param array $json The custom webhook payload
     * @return void
     */
    protected function handleCustom(array $json)
    {
        \App\Models\CustomWebhookLog::create([
            'payload' => $json,
        ]);
    }
}
