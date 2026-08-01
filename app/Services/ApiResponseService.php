<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;

class ApiResponseService
{
    public function success(mixed $data = null, string $message = 'OK', int $code = 200): JsonResponse
    {
        return response()->json($this->formatSuccess($data, $message), $code);
    }

    public function created(mixed $data = null, string $message = 'Created'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    public function noContent(string $message = 'Deleted successfully.'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => null,
        ], 200);
    }

    public function error(string $message, int $code = 400, mixed $errors = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if (! is_null($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $code);
    }

    public function unauthorized(string $message = 'Unauthenticated.'): JsonResponse
    {
        return $this->error($message, 401);
    }

    public function forbidden(string $message = 'Forbidden.'): JsonResponse
    {
        return $this->error($message, 403);
    }

    public function notFound(string $message = 'Resource not found.'): JsonResponse
    {
        return $this->error($message, 404);
    }

    public function validation(mixed $errors, string $message = 'Validation failed.'): JsonResponse
    {
        return $this->error($message, 422, $errors);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatSuccess(mixed $data, string $message): array
    {
        if ($data instanceof ResourceCollection && $data->resource instanceof AbstractPaginator) {
            $resolved = $data->response()->getData(true);

            return [
                'success' => true,
                'message' => $message,
                'data' => $resolved['data'] ?? [],
                'links' => $resolved['links'] ?? null,
                'meta' => $resolved['meta'] ?? null,
            ];
        }

        return [
            'success' => true,
            'message' => $message,
            'data' => $this->resolveData($data),
        ];
    }

    private function resolveData(mixed $data): mixed
    {
        if ($data instanceof JsonResource) {
            return $data->resolve();
        }

        if (is_array($data)) {
            return array_map(fn ($item) => $this->resolveData($item), $data);
        }

        return $data;
    }
}
