<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class Company extends Model
{
    protected $fillable = [
        'name',
        'website',
        'phone',
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
}