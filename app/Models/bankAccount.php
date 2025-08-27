<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $table = 'bank_accounts'; // Ensure this matches your table name
    protected $fillable = ['account_holder', 'account_number', 'bank_name', 'branch_code', 'account_type', 'status'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'bank_account_id');
    }

    public function reconciliations()
    {
        return $this->hasMany(Reconciliation::class, 'bank_account_id');
    }
}