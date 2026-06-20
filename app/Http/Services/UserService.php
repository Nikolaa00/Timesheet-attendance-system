<?php

namespace App\Http\Services;

use App\Models\User;

class UserService
{
    public function create(array $data): User
    {
        $data['is_active'] = true;
        $data['is_logged_in'] = false;
        $data['created_by'] = auth()->id();

        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        return $user;
    }

    public function getFiltered(
        ?string $search = null,
        string $status = 'active',
        string $sortBy = 'first_name',
        string $sortDirection = 'asc',
        int $limit = 10,
        int $offset = 0,
    ) {
        $query = User::query()
            ->with(['subsidiary', 'sector', 'shift'])
            ->whereIn('role', ['admin', 'employee']);

        $query->where('is_active', $status !== 'inactive');

        $direction = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';
        $sortColumn = $sortBy === 'last_name' ? 'last_name' : 'first_name';
        $secondarySortColumn = $sortColumn === 'first_name' ? 'last_name' : 'first_name';

        if ($search) {
            $term = '%' . trim($search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('username', 'like', $term)
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$term]);
            });
        }

        $total = $query->count();

        $items = $query->orderBy($sortColumn, $direction)
            ->orderBy($secondarySortColumn, $direction)
            ->skip($offset)
            ->take($limit)
            ->get();

        return ['items' => $items, 'total' => $total];
    }

    public function getAll()
    {
        return User::with(['subsidiary', 'sector', 'shift'])
            ->whereIn('role', ['admin', 'employee'])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }

    public function loadRelations(User $user): User
    {
        return $user->load(['subsidiary', 'sector', 'shift']);
    }

    public function deactivate(User $user, bool $isActive): User
    {
        $attributes = ['is_active' => $isActive];

        if (!$isActive) {
            $attributes['is_logged_in'] = false;
            $user->tokens()->delete();
        }

        $user->update($attributes);

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
