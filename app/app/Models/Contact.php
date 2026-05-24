<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
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
