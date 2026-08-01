<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projects,
    ) {}

    public function list(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $this->projects->paginateForUser($user, $perPage);
    }

    public function findForUser(User $user, int $projectId): Project
    {
        $project = $this->projects->findForUser($user, $projectId);

        abort_if($project === null, 404, 'Project not found.');

        return $project;
    }

    /**
     * @param  array{name: string, description?: string|null, status?: string, cover?: UploadedFile|null}  $data
     */
    public function create(User $user, array $data): Project
    {
        return DB::transaction(function () use ($user, $data) {
            $cover = $data['cover'] ?? null;
            unset($data['cover']);

            $project = $this->projects->create([
                ...$data,
                'user_id' => $user->id,
            ]);

            if ($cover instanceof UploadedFile) {
                $project->addMedia($cover)->toMediaCollection('cover');
            }

            return $project->fresh();
        });
    }

    /**
     * @param  array{name?: string, description?: string|null, status?: string, cover?: UploadedFile|null}  $data
     */
    public function update(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            $cover = $data['cover'] ?? null;
            unset($data['cover']);

            if ($data !== []) {
                $project = $this->projects->update($project, $data);
            }

            if ($cover instanceof UploadedFile) {
                $project->addMedia($cover)->toMediaCollection('cover');
            }

            return $project->fresh();
        });
    }

    public function delete(Project $project): void
    {
        $this->projects->delete($project);
    }
}
