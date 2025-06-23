<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'vendor_id',
        'amount',
        'category',
        'date'
    ];

    public function vendor() {
    return $this->belongsTo(Vendor::class);
    }

}
