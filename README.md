# Task Management REST API

Laravel REST API for a simple Task Management System with Sanctum authentication, Projects, Tasks, Dashboard stats, Spatie Media Library uploads, Pusher realtime task events, Service/Repository architecture, and a custom `ApiResponse` Facade.

## Requirements

- PHP 8.4+
- Composer
- SQLite (default) or MySQL
- Optional: Pusher account for realtime events

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan l5-swagger:generate
php artisan serve
```

Demo user after seeding:

- Email: `demo@example.com`
- Password: `password`

## Environment Setup

Key `.env` values:

```env
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# Or MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=task
# DB_USERNAME=root
# DB_PASSWORD=

QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1
```

Run the queue worker for overdue notifications and queued broadcasts:

```bash
php artisan queue:work
```

Schedule overdue checks (add to cron / Task Scheduler):

```bash
php artisan schedule:work
# or manually:
php artisan tasks:check-overdue
```

## Architecture

- **Controllers** — HTTP only, return via `ApiResponse` Facade
- **Form Requests** — validation
- **Services** — business logic, media, token handling, broadcast dispatch
- **Repositories** — Eloquent queries
- **Resources** — JSON transformers
- **Enums** — project status, task status/priority
- **Policies** — ownership checks
- **Media collections**
  - `User` → `avatar` (single)
  - `Project` → `cover` (single)
  - `Task` → `attachments` (multiple)

### ApiResponse Facade

All API success/error responses use:

```php
use App\Facades\ApiResponse;

return ApiResponse::success($data, 'OK');
return ApiResponse::created($data, 'Created');
return ApiResponse::validation($errors);
return ApiResponse::unauthorized();
```

Envelope:

```json
{
  "success": true,
  "message": "OK",
  "data": {}
}
```

JSON exception handling is configured in `bootstrap/app.php` and also uses this Facade.

## API Endpoints

Base URL: `/api`  
Auth header: `Authorization: Bearer {token}`

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/auth/register` | No | Register (+ optional avatar) |
| POST | `/auth/login` | No | Login |
| POST | `/auth/logout` | Yes | Logout |
| GET | `/me` | Yes | Profile |
| PUT/PATCH | `/me` | Yes | Update profile (+ avatar) |
| GET | `/projects` | Yes | List projects (paginated) |
| POST | `/projects` | Yes | Create project (+ cover) |
| GET | `/projects/{id}` | Yes | View project |
| PUT/PATCH | `/projects/{id}` | Yes | Update project |
| DELETE | `/projects/{id}` | Yes | Soft delete project |
| GET | `/projects/{id}/tasks` | Yes | List/filter/search tasks |
| POST | `/projects/{id}/tasks` | Yes | Create task (+ attachments) |
| GET | `/tasks/{id}` | Yes | View task |
| PUT/PATCH | `/tasks/{id}` | Yes | Update task |
| DELETE | `/tasks/{id}` | Yes | Soft delete task |
| GET | `/dashboard` | Yes | Aggregated stats |

### Task filters

`GET /api/projects/{id}/tasks?status=todo&priority=high&title=Review`

### Dashboard fields

`total_projects`, `active_projects`, `total_tasks`, `completed_tasks`, `pending_tasks`, `overdue_tasks`

### Example requests

```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"demo@example.com\",\"password\":\"password\"}"

# Create project
curl -X POST http://localhost:8000/api/projects \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN" \
  -F "name=Website Redesign" \
  -F "description=Demo" \
  -F "status=active" \
  -F "cover=@./cover.jpg"

# Filter tasks
curl "http://localhost:8000/api/projects/1/tasks?status=todo&priority=high" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN"
```

## Pusher Realtime

Task mutations broadcast on private channel `private-projects.{projectId}`:

| Event | Name |
|-------|------|
| Task created | `task.created` |
| Task updated | `task.updated` |
| Task deleted | `task.deleted` |

Channel auth endpoint: `POST /broadcasting/auth` with Sanctum bearer token.

Laravel Echo example:

```js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
  forceTLS: true,
  authEndpoint: '/broadcasting/auth',
  auth: {
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: 'application/json',
    },
  },
});

window.Echo.private(`projects.${projectId}`)
  .listen('.task.created', (e) => console.log('created', e))
  .listen('.task.updated', (e) => console.log('updated', e))
  .listen('.task.deleted', (e) => console.log('deleted', e));
```

## Documentation & Tools

- Swagger UI: [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)
- Postman collection: [`postman/Task_Management_API.postman_collection.json`](postman/Task_Management_API.postman_collection.json)
- SQL schema: use migrations in `database/migrations` (or `php artisan schema:dump` if desired)

## Tests

```bash
php artisan test
```

## Media Collections

Defined on models via `registerMediaCollections()`:

- User avatar
- Project cover
- Task attachments

## Bonus Features Included

- Repository + Service layers (all modules)
- Swagger / OpenAPI (`l5-swagger`)
- Feature & unit tests
- Postman collection
- Queue job for overdue task notifications
- Pusher realtime task events
- Custom `ApiResponse` Facade + JSON exception handling
