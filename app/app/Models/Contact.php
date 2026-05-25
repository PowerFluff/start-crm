<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
    ];
    
     /**
     * @return BelongsTo<Company, Contact>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
