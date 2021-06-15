<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;

class CostTypesQuery
{
    public function execute($projectId, $costTypeIds = [])
    {
        return DB::table('costs')
            ->select('cost_types.*', 'costs.amount')
            ->join('cost_types', function ($join) {
                $join->on('costs.cost_type_id', '=', 'cost_types.id');
            })
            ->where('project_id', $projectId)
            ->when(
                !empty($costTypeIds),
                fn ($query) => $query->whereIn(
                    'costs.id',
                    $this->getFilteredCostIds($projectId, $costTypeIds)
                )
            )
            ->get();
    }

    private function getFilteredCostIds($projectId, $costTypeIds)
    {
        $costTypeIds = implode(',', $costTypeIds);

        $result = DB::select(
            "with recursive selected_costs as (
                select costs.*, cost_types.parent_id from costs
                join cost_types on cost_types.id = costs.cost_type_id
                where cost_type_id in ($costTypeIds) and project_id = $projectId

                union

                select costs.*, cost_types.parent_id
                from costs
                join cost_types on cost_types.id = costs.cost_type_id
                join selected_costs on selected_costs.parent_id = cost_types.id
                where costs.project_id = $projectId
            ) select id from selected_costs"
        );

        return array_column($result, 'id');
    }
}
