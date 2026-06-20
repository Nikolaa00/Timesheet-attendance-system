<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterSectorsRequest;
use App\Http\Requests\FilterSectorUsersRequest;
use App\Http\Requests\StoreSectorRequest;
use App\Http\Requests\UpdateSectorRequest;
use App\Http\Resources\SectorResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\PaginatedListResponse;
use App\Http\Services\SectorService;
use App\Models\Sector;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;


class SectorController extends Controller
{
    protected SectorService $service;

    public function __construct(SectorService $service)
    {
        $this->service = $service;
    }

    public function index(FilterSectorsRequest $request)
    {
        $validated = $request->validated();

        $result = $this->service->getFiltered(
            search: $validated['search'] ?? null,
            sortBy: $validated['sort_by'] ?? 'name',
            sortDirection: $validated['sort_direction'] ?? 'asc',
            limit: (int) ($validated['limit'] ?? 10),
            offset: (int) ($validated['offset'] ?? 0),
        );

        return PaginatedListResponse::make(
            SectorResource::collection($result['items']),
            $result['total'],
        );
    }

    public function all()
    {
        $sectors = $this->service->getAll();

        return response()->json(SectorResource::collection($sectors), 200);
    }

    public function store(StoreSectorRequest $request)
    {
        $sector = $this->service->create($request->validated());
        $sector->load('subsidiaries')
            ->loadCount([
                'users',
                'users as shifts_count' => function (Builder $query) {
                    $query
                        ->select(DB::raw('count(distinct shift_id)'))
                        ->whereNotNull('shift_id');
                }
            ]);

        return response()->json( new SectorResource($sector), 201);
    }

    public function show(Sector $sector)
    {
        $sector->load('subsidiaries')
            ->loadCount([
                'users',
                'users as shifts_count' => function (Builder $query) {
                    $query
                        ->select(DB::raw('count(distinct shift_id)'))
                        ->whereNotNull('shift_id');
                }
            ]);

        return response()->json(new SectorResource($sector), 200);
    }

    /**
     * GET /sectors/{id}/users
     *
     * Returns paginated employees belonging to the sector identified by {id}.
     */
    public function users(int $id, FilterSectorUsersRequest $request): JsonResponse
    {
        $sector = Sector::findOrFail($id);
        $validated = $request->validated();

        $result = $this->service->getFilteredUsers(
            sector: $sector,
            search: $validated['search'] ?? null,
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

    public function update(UpdateSectorRequest $request, Sector $sector)
    {
        $sector = $this->service->update($sector, $request->validated());
        $sector->load('subsidiaries')
            ->loadCount([
                'users',
                'users as shifts_count' => function (Builder $query) {
                    $query
                        ->select(DB::raw('count(distinct shift_id)'))
                        ->whereNotNull('shift_id');
                }
            ]);

        return response()->json(new SectorResource($sector), 200);
    }

    public function destroy(Sector $sector)
    {
        $this->service->delete($sector);

        return response()->json([
            'message' => 'Sector deleted successfully',
        ], 200);
    }
}
