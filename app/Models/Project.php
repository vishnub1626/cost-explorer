<?php

namespace App\Models;

use App\Queries\CostTypesQuery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function costTypes($costTypeIds = [])
    {
        return (new CostTypesQuery)
            ->execute($this->id, $costTypeIds);
    }
}
