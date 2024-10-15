<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        // Add any exception types you want to exclude from logging
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // Check if the request expects a JSON response
        if ($request->expectsJson()) {
            // Handle specific exceptions for JSON response
            if ($exception instanceof ValidationException) {
                return $this->invalidJson($request, $exception);
            }

            // Handle other exceptions
            return response()->json(['message' => 'An error occurred.'], 500);
        }

        return parent::render($request, $exception);
    }

    /**
     * Customize the response for validation exceptions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'message' => 'Validation errors occurred.',
            'errors' => $exception->errors(),
        ], 422);
    }

    // Add any additional methods or custom handling if needed
}
