<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterSubsidiariesRequest;
use App\Http\Requests\StoreSubsidiaryRequest;
use App\Http\Requests\UpdateSubsidiaryRequest;
use App\Http\Resources\SubsidiaryResource;
use App\Http\Resources\PaginatedListResponse;
use App\Http\Services\SubsidiaryService;
use App\Models\Subsidiary;

class SubsidiaryController extends Controller
{
    protected SubsidiaryService $service;

    public function __construct(SubsidiaryService $service)
    {
        $this->service = $service;
    }

    public function index(FilterSubsidiariesRequest $request)
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
            SubsidiaryResource::collection($result['items']),
            $result['total'],
        );
    }

    public function all()
    {
        $subsidiaries = $this->service->getAll();

        return response()->json(
            SubsidiaryResource::collection($subsidiaries),
        );
    }

    public function show(Subsidiary $subsidiary)
    {
        $subsidiary->load('parentCompany')->loadCount('sectors');
        return response()->json(new SubsidiaryResource($subsidiary));
    }

    public function store(StoreSubsidiaryRequest $request)
    {
        $subsidiary = $this->service->create($request->validated());

        return response()->json(new SubsidiaryResource($subsidiary), 201);
    }

    public function update(UpdateSubsidiaryRequest $request, Subsidiary $subsidiary)
    {
        $updated = $this->service->update($subsidiary, $request->validated());

        return response()->json(new SubsidiaryResource($updated));
    }

    public function destroy(Subsidiary $subsidiary)
    {
        $this->service->delete($subsidiary);

        return response()->json([
            "message" => "Subsidiary deleted successfully."
        ], 200);
    }
}
