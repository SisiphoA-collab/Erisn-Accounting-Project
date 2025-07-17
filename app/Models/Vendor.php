<?php

namespace App\Models;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'balance'
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function expenses() {
    return $this->hasMany(Expense::class);
    }

}
