<?php

namespace App\Models;

use App\Models\Learner;
use Illuminate\Database\Eloquent\Model;

class Stipend extends Model
{
    protected $fillable = ['learner_id', 'amount', 'status', 'month', 'receipt_path'];

    public function learner()
    {
        return $this->belongsTo(Learner::class);
    }
}

