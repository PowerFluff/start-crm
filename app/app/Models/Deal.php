<?php

namespace App\Models;

use App\Enums\DealStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Deal extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'amount',
        'status',
        'expected_close_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => DealStatus::class,
            'expected_close_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Company, Deal>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @param Builder<Deal> $query
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query
            ->when($filters['status'] ?? null, function (Builder $query, string $status) {
                $query->where('status', $status);
            })
            ->when($filters['company_id'] ?? null, function (Builder $query, string $companyId) {
                $query->where('company_id', $companyId);
            });
    }

    /**
     * @return HasMany<Task, Deal>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}