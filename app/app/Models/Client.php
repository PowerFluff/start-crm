<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'comment',
    ];

    /**
     * @param Builder<Client> $query
     */
    public function scopeSearch(Builder $query, string $search): void
    {
        $operator = $query->getConnection()->getDriverName() === 'pgsql'
            ? 'ilike'
            : 'like';

        $query->when($search !== '', function (Builder $query) use ($search, $operator) {
            $query->where(function (Builder $query) use ($search, $operator) {
                $query
                    ->where('name', $operator, "%{$search}%")
                    ->orWhere('email', $operator, "%{$search}%")
                    ->orWhere('phone', $operator, "%{$search}%")
                    ->orWhere('company', $operator, "%{$search}%");
            });
        });
    }
}