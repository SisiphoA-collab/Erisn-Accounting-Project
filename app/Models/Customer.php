<?php

namespace App\Models;

use App\Models\Invoice;
use App\Models\Customer;
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

    Schema::table('customers', function (Blueprint $table) {
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
});

}
