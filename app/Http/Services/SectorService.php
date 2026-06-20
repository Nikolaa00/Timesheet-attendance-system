<?php

namespace App\Http\Services;

use App\Models\Sector;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SectorService
{
    public function getFiltered(
        ?string $search = null,
        string $sortBy = 'name',
        string $sortDirection = 'asc',
        int $limit = 10,
        int $offset = 0,
    ) {
        $query = Sector::query()
            ->with('subsidiaries')
            ->withCount('users')
            ->withCount([
                'users as shifts_count' => function (Builder $query): void {
                    $query
                        ->select(DB::raw('count(distinct shift_id)'))
                        ->whereNotNull('shift_id');
                },
            ]);

        if ($search) {
            $searchTerm = '%' . trim($search) . '%';

            $query->where(function ($q) use ($searchTerm): void {
                $q->where('name', 'like', $searchTerm)
                    ->orWhereHas('subsidiaries', function ($subsidiaryQuery) use ($searchTerm): void {
                        $subsidiaryQuery->where('name', 'like', $searchTerm);
                    });
            });
        }

        $direction = strtolower($sortDirection) === 'desc'
            ? 'desc'
            : 'asc';

        $total = $query->count();

        $items = $query->orderBy($sortBy, $direction)
            ->skip($offset)
            ->take($limit)
            ->get();

        return ['items' => $items, 'total' => $total];
    }

    public function getAll()
    {
        return Sector::with('subsidiaries')
            ->withCount('users')
            ->withCount([
                'users as shifts_count' => function (Builder $query): void {
                    $query
                        ->select(DB::raw('count(distinct shift_id)'))
                        ->whereNotNull('shift_id');
                },
            ])
            ->get();
    }

    public function create(array $data): Sector
    {
        $subsidiaryIds = $data['subsidiary_ids'] ?? [];
        unset($data['subsidiary_ids']);

        $sector = Sector::create($data);
        $sector->subsidiaries()->sync($subsidiaryIds);

        return $sector;
    }

    public function update(Sector $sector, array $data): Sector
    {
        $subsidiaryIds = $data['subsidiary_ids'] ?? null;
        unset($data['subsidiary_ids']);

        if (is_array($subsidiaryIds) && count($subsidiaryIds) === 0) {
            throw ValidationException::withMessages([
                'subsidiary_ids' => 'A sector must belong to at least one subsidiary.'
            ]);
        }

        $sector->update($data);

        if (is_array($subsidiaryIds)) {
            $sector->subsidiaries()->sync($subsidiaryIds);
        }

        return $sector;
    }

    public function countShifts(Sector $sector): int
    {
        return $sector->users()
            ->whereNotNull('shift_id')
            ->count(DB::raw('DISTINCT shift_id'));
    }

    public function delete(Sector $sector): void
    {
        if ($sector->users()->exists()) {
            throw ValidationException::withMessages([
                'error' => 'This sector cannot be deleted because it has users.'
            ]);
        }

        if ($sector->subsidiaries()->count() > 1) {
            throw ValidationException::withMessages([
                'error' => 'This sector belongs to multiple subsidiaries. Remove it from specific subsidiaries instead of deleting it.'
            ]);
        }

        $sector->delete();
    }

    /**
     * Return paginated employees assigned to the given sector.
     */
    public function getFilteredUsers(
        Sector $sector,
        ?string $search = null,
        string $sortBy = 'first_name',
        string $sortDirection = 'asc',
        int $limit = 10,
        int $offset = 0,
    ): array {
        $query = $sector->users()
            ->with(['subsidiary', 'sector', 'shift'])
            ->whereIn('role', ['admin', 'employee']);

        $direction = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';
        $sortColumn = $sortBy === 'last_name' ? 'last_name' : 'first_name';
        $secondarySortColumn = $sortColumn === 'first_name' ? 'last_name' : 'first_name';

        if ($search) {
            $term = '%' . trim($search) . '%';
            $query->where(function ($q) use ($term): void {
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
}
