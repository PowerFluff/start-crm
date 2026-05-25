<?php

namespace App\Http\Resources;

use App\Http\Resources\ContactResource;
use App\Http\Resources\DealResource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'website' => $this->website,
            'phone' => $this->phone,
            'contacts' => ContactResource::collection($this->whenLoaded('contacts')),
            'deals' => DealResource::collection($this->whenLoaded('deals')),
            'owner_id' => $this->owner_id,
            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner->id,
                    'name' => $this->owner->name,
                    'email' => $this->owner->email,
                ];
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}