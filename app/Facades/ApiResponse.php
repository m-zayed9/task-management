<?php

namespace App\Facades;

use App\Services\ApiResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static JsonResponse success(mixed $data = null, string $message = 'OK', int $code = 200)
 * @method static JsonResponse created(mixed $data = null, string $message = 'Created')
 * @method static JsonResponse noContent(string $message = 'Deleted successfully.')
 * @method static JsonResponse error(string $message, int $code = 400, mixed $errors = null)
 * @method static JsonResponse unauthorized(string $message = 'Unauthenticated.')
 * @method static JsonResponse forbidden(string $message = 'Forbidden.')
 * @method static JsonResponse notFound(string $message = 'Resource not found.')
 * @method static JsonResponse validation(mixed $errors, string $message = 'Validation failed.')
 *
 * @see ApiResponseService
 */
class ApiResponse extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ApiResponseService::class;
    }
}
