<?php

use App\Models\Project;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('projects.{projectId}', function ($user, int $projectId) {
    return Project::query()
        ->whereKey($projectId)
        ->where('user_id', $user->id)
        ->exists();
});
