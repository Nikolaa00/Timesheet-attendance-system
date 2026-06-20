<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeactivateUserRequest;
use App\Http\Requests\FilterEmployeesRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserDeactivateResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\PaginatedListResponse;
use App\Http\Services\UserService;
use App\Models\User;

class UserController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index(FilterEmployeesRequest $request)
    {
        $validated = $request->validated();

        $result = $this->service->getFiltered(
            search: $validated['search'] ?? null,
            status: $validated['status'] ?? 'active',
            sortBy: $validated['sort_by'] ?? 'first_name',
            sortDirection: $validated['sort_direction'] ?? 'asc',
            limit: (int) ($validated['limit'] ?? 10),
            offset: (int) ($validated['offset'] ?? 0),
        );

        return PaginatedListResponse::make(
            UserResource::collection($result['items']),
            $result['total'],
        );
    }

    public function all()
    {
        $users = $this->service->getAll();

        return response()->json(UserResource::collection($users), 200);
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->service->create($request->validated());
        $userWithRelations = $this->service->loadRelations($user);

        return response()->json(new UserResource($userWithRelations), 201);
    }

    public function show(User $user)
    {
        $userWithRelations = $this->service->loadRelations($user);

        return response()->json(new UserResource($userWithRelations));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $updated = $this->service->update($user, $request->validated());
        $userWithRelations = $this->service->loadRelations($updated);

        return response()->json(new UserResource($userWithRelations));
    }

    public function deactivate(DeactivateUserRequest $request, User $user)
    {
        $updated = $this->service->deactivate($user, $request->validated('is_active'));

        return response()->json(new UserDeactivateResource($updated), 200);
    }

    public function destroy(User $user)
    {
        $this->service->delete($user);

        return response()->json([
            "message" => "User deleted successfully."
        ], 200);
    }
}
