<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;

class CostTypesQuery
{
    public function execute($projectIds, $costTypeIds = [])
    {
        return DB::table('costs')
            ->select('cost_types.*', 'costs.amount', 'costs.project_id')
            ->join('cost_types', function ($join) {
                $join->on('costs.cost_type_id', '=', 'cost_types.id');
            })
            ->whereIn('project_id', $projectIds)
            ->when(
                !empty($costTypeIds),
                fn ($query) => $query->whereIn(
                    'costs.id',
                    $this->getFilteredCostIds($projectIds, $costTypeIds)
                )
            )
            ->get();
    }

    private function getFilteredCostIds($projectIds, $costTypeIds)
    {
        $projectIds = implode(',', $projectIds);
        $costTypeIds = implode(',', $costTypeIds);

        $result = DB::select(
            "with recursive selected_costs as (
                select costs.*, cost_types.parent_id from costs
                join cost_types on cost_types.id = costs.cost_type_id
                where cost_type_id in ($costTypeIds) and project_id in ($projectIds)

                union

                select costs.*, cost_types.parent_id
                from costs
                join cost_types on cost_types.id = costs.cost_type_id
                join selected_costs on selected_costs.parent_id = cost_types.id
                where costs.project_id in ($projectIds)
            ) select id from selected_costs"
        );

        return array_column($result, 'id');
    }
}
