<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function index(): JsonResponse
    {
        $contacts = Contact::query()
            ->with('company')
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => ContactResource::collection($contacts),
            'meta' => [
                'current_page' => $contacts->currentPage(),
                'per_page' => $contacts->perPage(),
                'total' => $contacts->total(),
                'last_page' => $contacts->lastPage(),
            ],
        ]);
    }

    public function store(StoreContactRequest $request): JsonResponse
    {
        $contact = Contact::query()->create($request->validated());

        return response()->json([
            'data' => new ContactResource($contact),
        ], 201);
    }

    public function show(Contact $contact): JsonResponse
    {
        $contact->load('company');

        return response()->json([
            'data' => new ContactResource($contact),
        ]);
    }

    public function update(UpdateContactRequest $request, Contact $contact): JsonResponse
    {
        $contact->update($request->validated());

        return response()->json([
            'data' => new ContactResource($contact),
        ]);
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();

        return response()->json(status: 204);
    }
}