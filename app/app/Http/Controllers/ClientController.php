<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function index(): JsonResponse
    {
        $clients = Client::query()
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => ClientResource::collection($clients),
            'meta' => [
                'current_page' => $clients->currentPage(),
                'per_page' => $clients->perPage(),
                'total' => $clients->total(),
                'last_page' => $clients->lastPage(),
            ],
        ]);
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = Client::create($request->validated());

        return response()->json([
            'data' => new ClientResource($client),
        ], 201);
    }

    public function show(Client $client): JsonResponse
    {
        return response()->json([
            'data' => new ClientResource($client),
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client): JsonResponse
    {
        $client->update($request->validated());

        return response()->json([
            'data' => new ClientResource($client),
        ]);
    }

    public function destroy(Client $client): JsonResponse
    {
        $client->delete();

        return response()->json(status: 204);
    }
}