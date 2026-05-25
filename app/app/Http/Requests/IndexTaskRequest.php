<?php

namespace App\Http\Requests;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'deal_id' => ['nullable', 'integer', 'exists:deals,id'],
            'status' => ['nullable', 'string', Rule::in(TaskStatus::values())],
        ];
    }
}