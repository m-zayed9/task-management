<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/auth/register',
    tags: ['Auth'],
    summary: 'Register a new user',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password'),
                    new OA\Property(property: 'avatar', type: 'string', format: 'binary'),
                ]
            )
        )
    ),
    responses: [new OA\Response(response: 201, description: 'Registered')]
)]
#[OA\Post(
    path: '/auth/login',
    tags: ['Auth'],
    summary: 'Login and receive Sanctum token',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'password', type: 'string'),
            ]
        )
    ),
    responses: [new OA\Response(response: 200, description: 'Logged in')]
)]
#[OA\Post(
    path: '/auth/logout',
    tags: ['Auth'],
    summary: 'Logout current token',
    security: [['sanctum' => []]],
    responses: [new OA\Response(response: 200, description: 'Logged out')]
)]
#[OA\Get(
    path: '/me',
    tags: ['Profile'],
    summary: 'Get authenticated profile',
    security: [['sanctum' => []]],
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
#[OA\Get(
    path: '/projects',
    tags: ['Projects'],
    summary: 'List projects',
    security: [['sanctum' => []]],
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
#[OA\Post(
    path: '/projects',
    tags: ['Projects'],
    summary: 'Create project',
    security: [['sanctum' => []]],
    responses: [new OA\Response(response: 201, description: 'Created')]
)]
#[OA\Get(
    path: '/projects/{project}',
    tags: ['Projects'],
    summary: 'View project',
    security: [['sanctum' => []]],
    parameters: [new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
#[OA\Put(
    path: '/projects/{project}',
    tags: ['Projects'],
    summary: 'Update project',
    security: [['sanctum' => []]],
    parameters: [new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
#[OA\Delete(
    path: '/projects/{project}',
    tags: ['Projects'],
    summary: 'Delete project',
    security: [['sanctum' => []]],
    parameters: [new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
    responses: [new OA\Response(response: 200, description: 'Deleted')]
)]
#[OA\Get(
    path: '/projects/{project}/tasks',
    tags: ['Tasks'],
    summary: 'List tasks with filters',
    security: [['sanctum' => []]],
    parameters: [
        new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['todo', 'in_progress', 'done'])),
        new OA\Parameter(name: 'priority', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['low', 'medium', 'high'])),
        new OA\Parameter(name: 'title', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
    ],
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
#[OA\Post(
    path: '/projects/{project}/tasks',
    tags: ['Tasks'],
    summary: 'Create task',
    security: [['sanctum' => []]],
    parameters: [new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
    responses: [new OA\Response(response: 201, description: 'Created')]
)]
#[OA\Get(
    path: '/tasks/{task}',
    tags: ['Tasks'],
    summary: 'View task',
    security: [['sanctum' => []]],
    parameters: [new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
#[OA\Put(
    path: '/tasks/{task}',
    tags: ['Tasks'],
    summary: 'Update task (broadcasts task.updated)',
    security: [['sanctum' => []]],
    parameters: [new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
#[OA\Delete(
    path: '/tasks/{task}',
    tags: ['Tasks'],
    summary: 'Delete task (broadcasts task.deleted)',
    security: [['sanctum' => []]],
    parameters: [new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
    responses: [new OA\Response(response: 200, description: 'Deleted')]
)]
#[OA\Get(
    path: '/dashboard',
    tags: ['Dashboard'],
    summary: 'Dashboard statistics',
    security: [['sanctum' => []]],
    responses: [new OA\Response(response: 200, description: 'OK')]
)]
class Paths
{
}
