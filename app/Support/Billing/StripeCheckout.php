<?php

namespace App\Support\Billing;

use App\Models\TenantPlanApplication;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class StripeCheckout
{
    public function createCheckoutSession(TenantPlanApplication $application, array $plan): array
    {
        $response = $this->client()
            ->asForm()
            ->post('https://api.stripe.com/v1/checkout/sessions', $this->checkoutPayload($application, $plan));

        if ($response->failed()) {
            throw new RuntimeException($this->errorMessage($response));
        }

        if (blank($response->json('id')) || blank($response->json('url'))) {
            throw new RuntimeException('Stripe returned an incomplete checkout session response.');
        }

        return [
            'session_id' => (string) $response->json('id'),
            'payment_intent_id' => (string) ($response->json('payment_intent') ?? ''),
            'subscription_id' => (string) ($response->json('subscription') ?? ''),
            'url' => (string) $response->json('url'),
        ];
    }

    public function retrieveCheckoutSession(string $sessionId): array
    {
        $response = $this->client()
            ->get('https://api.stripe.com/v1/checkout/sessions/'.$sessionId);

        if ($response->failed()) {
            throw new RuntimeException($this->errorMessage($response));
        }

        return $response->json();
    }

    public function isConfigured(): bool
    {
        return filled(config('services.stripe.secret'));
    }

    protected function checkoutPayload(TenantPlanApplication $application, array $plan): array
    {
        $payload = [
                'mode' => 'subscription',
                'customer_email' => $application->contact_email,
                'success_url' => route('central.plan-applications.success', $application).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('central.plan-applications.cancel', $application),
                'metadata[application_id]' => (string) $application->getKey(),
                'line_items[0][quantity]' => 1,
            ];

        $priceId = trim((string) ($plan['stripe_price_id'] ?? ''));

        if ($priceId !== '') {
            $payload['line_items[0][price]'] = $priceId;

            return $payload;
        }

        $amount = (int) ($plan['amount'] ?? 0);

        if ($amount < 1) {
            throw new RuntimeException('Stripe plan amount is invalid for the selected plan.');
        }

        $payload['line_items[0][price_data][currency]'] = strtolower((string) ($plan['currency'] ?? 'php'));
        $payload['line_items[0][price_data][unit_amount]'] = $amount;
        $payload['line_items[0][price_data][recurring][interval]'] = 'month';
        $payload['line_items[0][price_data][product_data][name]'] = 'University Practicum '.$plan['label'].' Plan';

        return $payload;
    }

    protected function client()
    {
        $request = Http::withToken((string) config('services.stripe.secret'));
        $caBundle = trim((string) config('services.stripe.ca_bundle', ''));

        if ($caBundle !== '') {
            return $request->withOptions([
                'verify' => $caBundle,
            ]);
        }

        if (! (bool) config('services.stripe.verify_ssl', true)) {
            return $request->withoutVerifying();
        }

        return $request;
    }

    protected function errorMessage(Response $response): string
    {
        $error = $response->json('error', []);

        if (is_array($error) && filled($error['message'] ?? null)) {
            return (string) $error['message'];
        }

        return 'Stripe request failed.';
    }
}
