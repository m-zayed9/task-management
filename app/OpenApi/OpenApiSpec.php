<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Task Management API',
    description: 'RESTful API for a Task Management System with Sanctum auth, projects, tasks, dashboard stats, media uploads, and Pusher realtime events.'
)]
#[OA\Server(url: '/api', description: 'API base path')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token',
    description: 'Laravel Sanctum personal access token'
)]
#[OA\Tag(name: 'Auth')]
#[OA\Tag(name: 'Profile')]
#[OA\Tag(name: 'Projects')]
#[OA\Tag(name: 'Tasks')]
#[OA\Tag(name: 'Dashboard')]
class OpenApiSpec
{
}
