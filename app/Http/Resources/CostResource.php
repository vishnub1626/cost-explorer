<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->cost_type_id,
            'name' => $this->type->name,
            'amount' => (float) $this->amount,
            'type' => 'cost',
            'children' => [],
        ];

    }
}
