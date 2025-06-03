<?php

namespace App\Models;

use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
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

    public function invoices() {
        return $this->hasMany(Invoice::class);
    }

}
