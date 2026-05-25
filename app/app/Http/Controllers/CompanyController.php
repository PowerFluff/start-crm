<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource; 

use App\Models\Company;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    public function index(): JsonResponse
    {
        $search = request()->string('search')->toString();

        $companies = Company::query()
            ->with('owner')
            ->search($search)
            ->latest()
            ->paginate(10);
        
        return response()->json([
            'data' => CompanyResource::collection($companies),
            'meta' => [
                'current_page' => $companies->currentPage(),
                'per_page' => $companies->perPage(),
                'total' => $companies->total(),
                'last_page' => $companies->lastPage(),
            ],
        ]);
    }

    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $company = $request->user()->companies()->create(
            $request->safe()->except('owner_id')
        );

        $company->load('owner');

        return response()->json([
            'data' => new CompanyResource($company),
        ], 201);
    }

    public function show(Company $company): JsonResponse
    {
        $company->load(['owner', 'contacts', 'deals']);

        return response()->json([
            'data' => new CompanyResource($company),
        ]);
    }

    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $company->update($request->validated());

        return response()->json([
            'data' => new CompanyResource($company),
        ]);
    }

    public function destroy(Company $company): JsonResponse
    {
        $company->delete();

        return response()->json(status: 204);
    }
}