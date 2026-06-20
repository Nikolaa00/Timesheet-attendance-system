<?php
namespace App\Http\Services;

use App\Models\Subsidiary;
use Illuminate\Validation\ValidationException;
class SubsidiaryService
{
    public function create(array $data): Subsidiary
    {
        $firstCompany = Subsidiary::first();

        if (!$firstCompany) {
            $data['parent_company_id'] = null;
        } else {
            $data['parent_company_id'] = $firstCompany->id;
        }

        return Subsidiary::create($data);
    }

    public function update(Subsidiary $subsidiary, array $data): Subsidiary
    {
        $subsidiary->update($data);
        return $subsidiary;
    }

    public function getFiltered(
        ?string $search = null,
        string $sortBy = 'name',
        string $sortDirection = 'asc',
        int $limit = 10,
        int $offset = 0,
    ) {
        $query = Subsidiary::query()
            ->with('parentCompany')
            ->withCount('sectors');

        if ($search) {
            $searchTerm = '%' . trim($search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('address', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('phone_number', 'like', $searchTerm);
            });
        }

        $direction = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';

        $total = $query->count();

        $items = $query->orderBy($sortBy, $direction)
            ->skip($offset)
            ->take($limit)
            ->get();

        return ['items' => $items, 'total' => $total];
    }

    public function getAll()
    {
        return Subsidiary::with('parentCompany')
            ->withCount('sectors')
            ->get();
    }

    public function delete(Subsidiary $subsidiary): void
    {
        if ($subsidiary->children()->exists()) {
            throw ValidationException::withMessages([
                'error' => 'This company cannot be deleted because it has subsidiaries. Please move or delete the subsidiaries first.'
            ]);
        }

        if ($subsidiary->sectors()->exists()) {
            throw ValidationException::withMessages([
                'error' => 'This company cannot be deleted because it has sectors. Please move or delete the sectors first.'
            ]);
        }
        $subsidiary->delete();
    }
}