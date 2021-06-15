<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function costs()
    {
        return $this->hasMany(Cost::class);
    }

    public function costTypes()
    {
        return $this->belongsToMany(
            CostType::class,
            'costs'
        )->as('cost')->withPivot('amount');
    }
}
