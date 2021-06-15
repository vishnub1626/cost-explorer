<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Resources\ClientResource;

class CostExplorerController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::query()
            ->with('projects')
            ->get();

        return ClientResource::collection($clients);
    }
}
