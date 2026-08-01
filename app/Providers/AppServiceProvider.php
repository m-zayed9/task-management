<?php

namespace App\Providers;

use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\DashboardRepository;
use App\Repositories\Eloquent\ProjectRepository;
use App\Repositories\Eloquent\TaskRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Services\ApiResponseService;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ApiResponseService::class);

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(DashboardRepositoryInterface::class, DashboardRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        AliasLoader::getInstance()->alias('ApiResponse', \App\Facades\ApiResponse::class);

        Broadcast::routes(['middleware' => ['auth:sanctum']]);
    }
}
