<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SearchUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

/**
 * This controller is responsible for handling global search requests.
 */
class SearchController extends Controller
{
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
}
