<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function type()
    {
        return $this->belongsTo(CostType::class, 'cost_type_id');
    }
}
