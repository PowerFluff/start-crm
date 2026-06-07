<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource; 

use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CompanyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->toString();
    
        $companies = $request->user()
            ->companies()
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

    public function show(Request $request, Company $company): JsonResponse
    {
        $this->authorize('view', $company);

        $company->load(['owner', 'contacts', 'deals']);

        return response()->json([
            'data' => new CompanyResource($company),
        ]);
    }

    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $this->authorize('update', $company);

        $company->update($request->validated());

        return response()->json([
            'data' => new CompanyResource($company),
        ]);
    }

    public function destroy(Request $request, Company $company): JsonResponse
    {
        $this->authorize('delete', $company);

        $company->delete();

        return response()->json(status: 204);
    }
}