<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name', 
        'industry', 
        'logo', 
        'settings'];

    // A company has many customers
    public function customers() {
        return $this->hasMany(Customer::class);
    }

    // A company has many vendors
    public function vendors() {
        return $this->hasMany(Vendor::class);
    }

    // A company has many accounts
    public function accounts() {
        return $this->hasMany(Account::class);
    }
}
