<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GlobalSearchRequest;
use App\Http\Requests\Api\SearchUserRequest;
use App\Http\Resources\GlobalSearchResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

/**
 * This controller is responsible for handling global search requests.
 */
class SearchController extends Controller
{
    /**
     * Search for users.
     *
     * @param SearchUserRequest $request
     *
     * @return UserResource
     */
    public function users(SearchUserRequest $request)
    {
        return UserResource::collection(
            User::query()
                ->whereIn('id', User::search($request->input('query'))->keys())
                ->whereNotIn('id', $request->input('except', []))
                ->limit($request->input('limit', 5))
                ->get()
        );
    }

    /**
     * Performs search on multiple resources.
     *
     * @param GlobalSearchRequest $request
     *
     * @return GlobalSearchResource
     */
    public function global(GlobalSearchRequest $request): GlobalSearchResource
    {
        $query = $request->input('query');

        return new GlobalSearchResource([
            'users' => UserResource::collection(
                User::query()
                    ->whereIn('id', User::search($query)->keys())
                    ->limit(5)
                    ->get()
            ),
            'projects' => ProjectResource::collection(
                Project::query()
                    ->assignedTo($request->user())
                    ->whereIn('id', Project::search($query)->keys())
                    ->limit(5)
                    ->get()
            ),
            'tasks' => TaskResource::collection(
                Task::query()
                    ->assignedTo($request->user())
                    ->whereIn('id', Task::search($query)->keys())
                    ->limit(5)
                    ->get()
            ),
        ]);
    }
}
