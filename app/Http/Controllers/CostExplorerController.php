<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Support\UpdateAmounts;
use App\Queries\CostTypesQuery;
use App\Support\CostTreeBuilder;

class CostExplorerController extends Controller
{
    public function index()
    {
        $clients = Client::query()
            ->when(
                request('client_id'),
                fn ($query, $clientIds) => $query->whereIn('id', $clientIds)
            )
            ->when(
                request('project_id'),
                fn ($query, $projectIds) => $query->whereHas(
                    'projects',
                    fn ($query) => $query->whereIn('id', $projectIds)
                )->with([
                    'projects' => fn ($query) => $query->whereIn('id', request('project_id'))
                ]),
                fn ($query) => $query->with('projects')
            )
            ->get();

        $costTypes = (new CostTypesQuery)
            ->execute(
                $clients->pluck('projects')->flatten()->pluck('id')->toArray(),
                request('cost_type_id')
            );

        $clients = $clients->map(
            fn ($client) => (object) [
                'id' => $client->id,
                'name' => $client->name,
                'type' => 'client',
                'amount' => 0,
                'children' => $client->projects->map(
                    fn ($project) => (object) [
                        'id' => $project->id,
                        'name' => $project->title,
                        'type' => 'project',
                        'amount' => 0,
                        'children' => (new CostTreeBuilder)
                            ->execute($costTypes->where('project_id', $project->id))
                    ]
                )
            ]
        );

        return response()->json([
            'query' => urldecode(request()->fullUrl()),
            'data' => (new UpdateAmounts)->execute($clients)
        ]);
    }
}
