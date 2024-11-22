<?php

use App\Actions\CreateApiErrorResponseAction;
use App\Enums\ErrorCode;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Inertia\Inertia;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
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
        $exceptions->respond(function (Response $response, Throwable $e, Request $request) {
            // Format API (JSON) error responses to make them consistent
            if ($request->is('api/*')) {
                $createApiErrorResponse = resolve(CreateApiErrorResponseAction::class);
                return $createApiErrorResponse->execute($e);
            }

            // Handle 429 errors. Redirect back to the previous route with an Error Code
            if ($e instanceof ThrottleRequestsException) {
                return redirect()
                    ->back()
                    ->withHeaders($e->getHeaders())
                    ->withErrors([
                        ErrorCode::TOO_MANY_REQUESTS->value => 'Too many requests. Please try again in a minute.'
                    ]);
            }

            // Handle 404 errors.
            if ($e instanceof NotFoundHttpException) {
                return Inertia::render('ErrorPage', ['status' => Response::HTTP_NOT_FOUND])
                    ->toResponse($request)
                    ->setStatusCode(Response::HTTP_NOT_FOUND);
            }

            if (!app()->environment(['local', 'testing'])) {
                // An InvalidStateException from Socialite is likely an unauthorized request
                if ($e instanceof InvalidStateException) {
                    Log::warning('Socialite: ' . $e::class, [
                        'request_url' => $request->url(),
                        'user_agent' => $request->userAgent(),
                    ]);

                    $response->setStatusCode(Response::HTTP_FORBIDDEN);
                }

                // Log Server Errors
                if ($response->getStatusCode() >= Response::HTTP_INTERNAL_SERVER_ERROR) {
                    Log::error('Server Error: ' . $e::class, [
                        'error_message' => $e->getMessage(),
                        'stack_trace' => $e->getTraceAsString(),
                    ]);
                }

                /**
                 * Only show Inertia modal errors during local development and testing
                 * @see https://v2.inertiajs.com/error-handling
                 */
                $status = $response->getStatusCode();
                $message = $e->getMessage();
                return Inertia::render('ErrorPage', ['status' => $status, 'message' => $message])
                    ->toResponse($request)
                    ->setStatusCode($response->getStatusCode());
            }

            return $response;
        });
    })
    ->create();
