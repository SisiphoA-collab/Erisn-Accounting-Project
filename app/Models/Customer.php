<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{

    Use hasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'name',
        'email',
        'balance'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

}
