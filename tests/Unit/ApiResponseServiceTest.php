<?php

namespace Tests\Unit;

use App\Services\ApiResponseService;
use Tests\TestCase;

class ApiResponseServiceTest extends TestCase
{
    public function test_success_envelope(): void
    {
        $service = new ApiResponseService;
        $response = $service->success(['ok' => true], 'All good');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'success' => true,
            'message' => 'All good',
            'data' => ['ok' => true],
        ], $response->getData(true));
    }

    public function test_validation_envelope(): void
    {
        $service = new ApiResponseService;
        $response = $service->validation(['email' => ['Required']], 'Validation failed.');

        $this->assertSame(422, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['success']);
        $this->assertArrayHasKey('errors', $response->getData(true));
    }
}
