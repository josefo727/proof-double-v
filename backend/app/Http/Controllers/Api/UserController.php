<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('view-any', User::class);

        $search = $request->get('search', '');

        $users = User::query()
            ->search($search)
            ->latest()
            ->paginate();

        return response()->success(UserCollection::make($users));
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);

        $user = User::query()->create($validated);

        return response()->success(new UserResource($user), 'User created successfully', Response::HTTP_CREATED);
    }

    public function show(Request $request, User $user): JsonResponse
    {
        $this->authorize('view', $user);

        return response()->success(new UserResource($user));
    }

    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validated();

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->success(new UserResource($user), 'User updated successfully', Response::HTTP_OK);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->success(null, null, Response::HTTP_NO_CONTENT);
    }
}
