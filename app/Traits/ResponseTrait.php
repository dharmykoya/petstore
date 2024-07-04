<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Contracts\Support\Responsable;
use JsonSerializable;

trait ResponseTrait {
    /**
     * Return a generic successful HTTP response
     *
     * @param string $message
     * @param string|JsonSerializable|Responsable|null|array<string, mixed> $data
     * @param int $status
     * @return JsonResponse
     */
    public function successResponse(string $message, JsonSerializable|null|array|Responsable|string $data = null, int $status = 200): JsonResponse
    {
        return $this->jsonResponse($message, $status, $data);
    }


    /**
     * Return a generic client HTTP error response
     *
     * @param string $message
     * @param int $status
     * @param null | array<string, mixed> $error
     *
     * @return JsonResponse
     */
    public function failedResponse(string $message, int $status = 400, array $error = null): JsonResponse
    {
        return $this->jsonResponse($message, $status, $error);
    }


    /**
     * Return a generic server HTTP error response
     *
     * @param string $string
     * @param Exception|null $exception
     * @param int $status
     * @return JsonResponse
     */
    public function serverErrorResponse(string $string, Exception $exception = null, int $status = 500): JsonResponse
    {
        if ($exception !== null) {
            $error = "{$exception->getMessage()}
            on line {$exception->getLine()}
            in {$exception->getFile()}";

            Log::error($error);
        }

        return $this->jsonResponse($string, $status);
    }

    /**
     * Determine if a  HTTP status code indicates success
     */
    public function isStatusCodeSuccessful(int $status): bool
    {
        return $status >= 200 && $status < 300;
    }

    /**
     * Return a generic HTTP response
     * @param string $message
     * @param int $status
     * @param array<string, mixed> |null $data
     *
     * @return JsonResponse
     */
    public function jsonResponse(string $message, int $status, JsonSerializable|null|array|Responsable|string $data = null): JsonResponse
    {
        $is_successful = $this->isStatusCodeSuccessful($status);

        $response_data = [
            'status' => $is_successful,
            'message' => $message,
        ];

        if (! is_null($data)) {
            $response_data[$is_successful ? 'data' : 'error'] = $data;
        }

        return Response::json($response_data, $status);
    }
}
