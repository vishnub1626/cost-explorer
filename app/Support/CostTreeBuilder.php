<?php

namespace App\Support;

use Illuminate\Support\Collection;

class CostTreeBuilder
{
    public function execute($costTypes, $root = null) : Collection
    {
        $result = collect();

        foreach ($costTypes as $index => $costType) {
            if ($costType->parent_id == $root) {
                $costTypes->forget($index);
                $costType->children = $this->execute($costTypes, $costType->id);

                $result->push((object) [
                    'id' => $costType->id,
                    'name' => $costType->name,
                    'type' => 'cost',
                    'amount' => (float) $costType->cost->amount,
                    'children' => $costType->children,
                ]);
            }
        }

        return $result;
    }
}
