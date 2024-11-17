<?php

namespace App\Actions;

use App\Enums\ErrorCode;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * This action class handles the generation of consistent API error responses
 * for various exceptions that may occur in the application.
 *
 * It encapsulates the logic of determining the type of
 * error encountered and formatting a JSON response based on
 * the exception thrown. The responses include appropriate HTTP
 * status codes and error messages.
 *
 * Example (in bootstrap/app.php):
 * <code>
 * $exceptions->respond(function (Throwable $e) {
 *      if ($request->is('api/*')) {
 *          $createApiErrorResponse = resolve(CreateApiErrorResponseAction::class);
 *          return $createApiErrorResponse->execute($e);
 *      }
 *      return $response;
 * });
 * </code>
 *
 */
readonly class CreateApiErrorResponseAction
{
    public function execute(Throwable $e): JsonResponse
    {
        switch ($e) {
            // if route is not found
            case $e instanceof NotFoundHttpException:
            case $e instanceof MethodNotAllowedHttpException:
                $response = response()->json(
                    [
                        'success' => false,
                        'message' => 'Route not found',
                        'error_code' => ErrorCode::UNKNOWN_ROUTE,
                    ],
                    Response::HTTP_NOT_FOUND
                );
                break;
                // if we hit the app-level rate-limit
            case $e instanceof ThrottleRequestsException:
                $response = response()->json(
                    [
                        'success' => false,
                        'message' => 'Too many requests',
                        'error_code' => ErrorCode::TOO_MANY_REQUESTS,
                    ],
                    Response::HTTP_TOO_MANY_REQUESTS
                );
                break;
                // if we throw a validation error
            case $e instanceof ValidationException:
                $response = response()->json(
                    [
                        'success' => false,
                        'message' => 'A validation error has occurred',
                        'error_code' => ErrorCode::VALIDATION,
                        'errors' => $this->transformErrors($e),
                    ],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
                break;
                // if we throw an authentication error
            case $e instanceof AuthenticationException:
                $response = response()->json(
                    [
                        'success' => false,
                        'message' => 'Authentication error',
                        'error_code' => ErrorCode::UNAUTHORIZED,
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
                break;
            case $e instanceof UnauthorizedException: // Spatie Auth Exception
            case $e instanceof AuthorizationException: // Laravel Auth Exception
            case $e instanceof HttpException && $e->getStatusCode() === Response::HTTP_FORBIDDEN: // catch abort(403)
                $response = response()->json(
                    [
                        'success' => false,
                        'message' => $e->getMessage(),
                        'error_code' => ErrorCode::UNAUTHORIZED,
                    ],
                    Response::HTTP_FORBIDDEN
                );
                break;
                // if a model is not found (e.g. from Model::findOrFail)
            case $e instanceof ModelNotFoundException:
                $modelName = class_basename($e->getModel());
                $response = response()->json(
                    [
                        'success' => false,
                        'message' => "$modelName not found",
                        'error_code' => ErrorCode::RESOURCE_NOT_FOUND,
                    ],
                    Response::HTTP_NOT_FOUND
                );
                break;
            case $e instanceof PostTooLargeException:
                $response = response()->json(
                    [
                        'success' => false,
                        'message' => 'Request is too large',
                        'error_code' => ErrorCode::PAYLOAD_TOO_LARGE,
                    ],
                    Response::HTTP_REQUEST_ENTITY_TOO_LARGE
                );
                break;
            default:
                // if we f** up somewhere else
                Log::error($e->getMessage(), ['stack_trace' => $e->getTraceAsString()]);

                $body = [
                    'message' => $e->getMessage(),
                    'error_code' => ErrorCode::SERVER,
                    'stack_trace' => $e->getTraceAsString(),
                ];

                if (app()->environment('production', 'development')) {
                    $body['message'] = 'An unknown error has occurred';
                    unset($body['stack_trace']);
                }

                $response = response()->json($body, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * Transform validation error messages. We want consistent error formats.
     */
    private function transformErrors(ValidationException $exception): array
    {
        $errors = [];
        foreach ($exception->errors() as $field => $message) {
            $errors[] = ['field' => $field, 'messages' => $message];
        }

        return $errors;
    }
}
