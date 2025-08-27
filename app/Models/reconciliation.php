<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reconciliation extends Model
{
    protected $table = 'reconciliations'; // Explicitly define the table name
    protected $fillable = ['bank_account_id', 'date', 'status', 'notes']; // Fields from migration

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }
}