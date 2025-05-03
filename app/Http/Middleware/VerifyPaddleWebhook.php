<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Nyholm\Psr7\Factory\Psr17Factory;
use Paddle\SDK\Notifications\Secret;
use Paddle\SDK\Notifications\Verifier;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Response;

class VerifyPaddleWebhook
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $laravelRequest, Closure $next): Response
    {
        // Convert to PSR-7 request
        $psr17factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory(
            $psr17factory,
            $psr17factory,
            $psr17factory,
            $psr17factory
        );
        $psr17Request = $psrHttpFactory->createRequest($laravelRequest);

        // Get all secrets
        $secrets = [
            // config('services.paddle.webhook_secret_1'),
            // config('services.paddle.webhook_secret_2'),
            // config('services.paddle.webhook_secret_3'),
            config('services.paddle.cashier_webhook_secret'),
            config('services.paddle.sdk_webhook_secret')
        ];

        $verifier = new Verifier();
        $isVerified = false;

        // Try each secret one by one
        foreach ($secrets as $secret) {
            try {
                if ($verifier->verify($psr17Request, new Secret($secret))) {
                    $isVerified = true;
                    break;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        if (!$isVerified) {
            abort(403, 'Invalid Paddle webhook signature');
        }

        return $next($laravelRequest);
    }
}
