<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Display pricing plans page.
     */
    public function showPlans(): View
    {
        return view('news.pricing');
    }

    /**
     * Create Stripe Checkout session for Pro plan.
     */
    public function createCheckout(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('status', 'Please sign in to upgrade to Pro.');
        }

        $stripeSecret = config('services.stripe.secret') ?? env('STRIPE_SECRET');

        // Test mode / fallback checkout simulation if Stripe API keys aren't configured yet
        if (!$stripeSecret || str_contains($stripeSecret, 'your-stripe-secret-key')) {
            // Activate 30-day Pro trial for demonstration
            $user->update([
                'is_premium' => true,
                'premium_expires_at' => now()->addDays(30),
            ]);

            return redirect()->route('home')->with('status', '🎉 Pro Mode activated! Enjoy unlimited AI verifications and advanced diagnostics (30-day Test Subscription).');
        }

        try {
            \Stripe\Stripe::setApiKey($stripeSecret);

            $checkoutSession = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => $user->email,
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'VeriFact AI Pro Membership',
                            'description' => 'Unlimited AI credibility checks, image verification, and priority NLP analysis.',
                        ],
                        'unit_amount' => 1999, // $19.99
                        'recurring' => ['interval' => 'month'],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel'),
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ]);

            return redirect($checkoutSession->url);
        } catch (\Throwable $e) {
            Log::error('Stripe Checkout Error: ' . $e->getMessage());
            return back()->withErrors(['stripe' => 'Unable to connect to Stripe checkout. Please try again later.']);
        }
    }

    /**
     * Handle successful payment callback.
     */
    public function success(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            $user->update([
                'is_premium' => true,
                'premium_expires_at' => now()->addDays(30),
            ]);
        }

        return redirect()->route('home')->with('status', '🎉 Welcome to VeriFact AI Pro! Your subscription is now active.');
    }

    /**
     * Handle cancelled payment callback.
     */
    public function cancel(): RedirectResponse
    {
        return redirect()->route('home')->with('status', 'Checkout was cancelled. You can upgrade to Pro anytime.');
    }

    /**
     * Handle Stripe Webhook events.
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret') ?? env('STRIPE_WEBHOOK_SECRET');

        if (!$webhookSecret) {
            return response()->json(['status' => 'ignored']);
        }

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $userId = $session->metadata->user_id ?? null;

            if ($userId && ($user = User::find($userId))) {
                $user->update([
                    'is_premium' => true,
                    'stripe_customer_id' => $session->customer ?? null,
                    'premium_expires_at' => now()->addMonth(),
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
