<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'amount',
        'type',
        'date',
        'description',
 ];

    // protected $casts = [
    //     'transaction_date' => 'date',
    // ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    // public function creator()
    // {
    //     return $this->belongsTo(User::class, 'created_by');
    // }
}

