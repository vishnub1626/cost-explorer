<?php

namespace App\Support;

use Illuminate\Support\Collection;

class CostTreeBuilder
{
    public function execute($costTypes, $root = null) : Collection
    {
        $result = collect();

        foreach ($costTypes as $index => $cost) {
            if ($cost->parent_id == $root) {
                $costTypes->forget($index);
                $cost->children = $this->execute($costTypes, $cost->id);

                $result->push((object)[
                    'id' => $cost->id,
                    'name' => $cost->name,
                    'type' => 'cost',
                    'amount' => $cost->cost->amount,
                    'children' => $cost->children
                ]);
            }
        }

        return $result;
    }
}
