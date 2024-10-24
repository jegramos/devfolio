<?php

use App\Enums\ErrorCode;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Use redis for throttling requests
        $middleware->throttleWithRedis();

        // Set-up Inertia
        $middleware->web(append: [
            HandleInertiaRequests::class
        ]);

        $middleware->redirectGuestsTo(fn () => route('auth.login.showForm'));
        $middleware->redirectUsersTo(fn () => route('builder.resume.index'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle 429 status code
        $exceptions->render(function (ThrottleRequestsException $e) {
            return redirect()
                ->back()
                ->withHeaders($e->getHeaders())
                ->withErrors([
                    ErrorCode::TOO_MANY_REQUESTS->value => 'Too many login attempts. Please try again in a minute.'
                ]);
        });

        /**
         * Only show Inertia modal errors during local development and testing
         * @see https://v2.inertiajs.com/error-handling
         */
        $exceptions->respond(function (Response $response, Throwable $e, Request $request) {
            if (!app()->environment(['local', 'testing']) && in_array($response->getStatusCode(), [500, 503, 404, 403])) {
                return Inertia::render('ErrorPage', ['status' => $response->getStatusCode()])
                    ->toResponse($request)
                    ->setStatusCode($response->getStatusCode());
            }
            return $response;
        });
    })->create();
