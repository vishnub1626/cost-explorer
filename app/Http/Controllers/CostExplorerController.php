<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Support\CostTreeBuilder;
use App\Support\UpdateAmounts;

class CostExplorerController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::query()
            ->with('projects.costTypes')
            ->get();

        $clients = $clients->map(function ($client) {
            return (object) [
                'id' => $client->id,
                'name' => $client->name,
                'type' => 'client',
                'amount' => 0,
                'children' => $client->projects->map(function ($project) {
                    return (object) [
                        'id' => $project->id,
                        'name' => $project->title,
                        'type' => 'project',
                        'amount' => 0,
                        'children' => (new CostTreeBuilder)->execute($project->costTypes)
                    ];
                })
            ];
        });

        return response()->json([
            'data' => (new UpdateAmounts)->execute($clients)
        ]);
    }
}
