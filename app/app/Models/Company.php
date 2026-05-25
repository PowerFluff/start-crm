<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'website',
        'phone',
        'owner_id',
    ];

    /**
     * @param Builder<Company> $query
    */
    public function scopeSearch(Builder $query, string $search): void
    {
        $query->when($search !== '', function (Builder $query) use ($search) {
            $query->where(function (Builder $query) use ($search) {
                $query
                    ->where('name', 'ilike', "%{$search}%")
                    ->orWhere('website', 'ilike', "%{$search}%")
                    ->orWhere('phone', 'ilike', "%{$search}%");
            });
        });
    }

    /**
     * @return HasMany<Contact, Company>
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * @return HasMany<Deal, Company>
     */
    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    /**
     * @return BelongsTo<User, Company>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}