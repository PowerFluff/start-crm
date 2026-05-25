<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'deal_id' => $this->deal_id,
            'deal' => $this->whenLoaded('deal', function () {
                return [
                    'id' => $this->deal->id,
                    'title' => $this->deal->title,
                    'status' => $this->deal->status->value,
                ];
            }),
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status->value,
            'due_at' => $this->due_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}