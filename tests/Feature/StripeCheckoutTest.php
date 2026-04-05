<?php

namespace Tests\Feature;

use App\Models\TenantPlanApplication;
use App\Support\Billing\StripeCheckout;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StripeCheckoutTest extends TestCase
{
    public function test_it_uses_the_configured_price_id_when_present(): void
    {
        config()->set('services.stripe.secret', 'sk_test_example');

        Http::fake([
            'https://api.stripe.com/v1/checkout/sessions' => Http::response([
                'id' => 'cs_test_123',
                'url' => 'https://checkout.stripe.com/c/pay/cs_test_123',
                'subscription' => 'sub_test_123',
            ]),
        ]);

        $application = new TenantPlanApplication([
            'contact_email' => 'dean@example.test',
        ]);
        $application->setAttribute('id', 42);
        $application->exists = true;

        $checkout = app(StripeCheckout::class)->createCheckoutSession($application, [
            'label' => 'Premium',
            'amount' => 99900,
            'currency' => 'php',
            'stripe_price_id' => 'price_test_123',
        ]);

        $this->assertSame('cs_test_123', $checkout['session_id']);

        Http::assertSent(function (Request $request): bool {
            $data = $request->data();

            return $request->url() === 'https://api.stripe.com/v1/checkout/sessions'
                && ($data['line_items[0][price]'] ?? null) === 'price_test_123'
                && ! array_key_exists('line_items[0][price_data][unit_amount]', $data);
        });
    }

    public function test_it_builds_an_inline_recurring_price_when_price_id_is_missing(): void
    {
        config()->set('services.stripe.secret', 'sk_test_example');

        Http::fake([
            'https://api.stripe.com/v1/checkout/sessions' => Http::response([
                'id' => 'cs_test_456',
                'url' => 'https://checkout.stripe.com/c/pay/cs_test_456',
                'subscription' => 'sub_test_456',
            ]),
        ]);

        $application = new TenantPlanApplication([
            'contact_email' => 'coordinator@example.test',
        ]);
        $application->setAttribute('id', 99);
        $application->exists = true;

        $checkout = app(StripeCheckout::class)->createCheckoutSession($application, [
            'label' => 'Pro',
            'amount' => 59900,
            'currency' => 'php',
            'stripe_price_id' => null,
        ]);

        $this->assertSame('cs_test_456', $checkout['session_id']);

        Http::assertSent(function (Request $request): bool {
            $data = $request->data();

            return $request->url() === 'https://api.stripe.com/v1/checkout/sessions'
                && ($data['line_items[0][price_data][currency]'] ?? null) === 'php'
                && ($data['line_items[0][price_data][unit_amount]'] ?? null) === 59900
                && ($data['line_items[0][price_data][recurring][interval]'] ?? null) === 'month'
                && ($data['line_items[0][price_data][product_data][name]'] ?? null) === 'University Practicum Pro Plan';
        });
    }
}
