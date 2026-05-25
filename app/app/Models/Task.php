<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'deal_id',
        'title',
        'description',
        'status',
        'due_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'due_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Deal, Task>
     */
    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }
}