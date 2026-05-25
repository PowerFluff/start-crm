<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexDealRequest;
use App\Http\Requests\StoreDealRequest;
use App\Http\Requests\UpdateDealRequest;
use App\Http\Resources\DealResource;
use App\Models\Deal;
use Illuminate\Http\JsonResponse;

class DealController extends Controller
{
    public function index(IndexDealRequest $request): JsonResponse
    {
        $filters = $request->validated();

        $deals = Deal::query()
            ->with('company')
            ->filter($filters)
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => DealResource::collection($deals),
            'meta' => [
                'current_page' => $deals->currentPage(),
                'per_page' => $deals->perPage(),
                'total' => $deals->total(),
                'last_page' => $deals->lastPage(),
            ],
        ]);
    }

    public function store(StoreDealRequest $request): JsonResponse
    {
        $deal = Deal::query()->create($request->validated());

        $deal->load('company');

        return response()->json([
            'data' => new DealResource($deal),
        ], 201);
    }

    public function show(Deal $deal): JsonResponse
    {
        $deal->load(['company', 'tasks']);

        return response()->json([
            'data' => new DealResource($deal),
        ]);
    }

    public function update(UpdateDealRequest $request, Deal $deal): JsonResponse
    {
        $deal->update($request->validated());

        $deal->load('company');

        return response()->json([
            'data' => new DealResource($deal),
        ]);
    }

    public function destroy(Deal $deal): JsonResponse
    {
        $deal->delete();

        return response()->json(status: 204);
    }
}