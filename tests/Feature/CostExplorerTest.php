<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CostExplorerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_clients_without_any_projects()
    {
        $clients = Client::factory()->count(3)->create();

        $response = $this->json('GET', '/explorer');

        $response->assertStatus(200)
            ->assertExactJson([
                'data' => $clients->map(function ($client) {
                    return [
                        'id' => $client->id,
                        'name' => $client->name,
                        'amount' => 0,
                        'type' => 'client',
                        'children' => []
                    ];
                })->toArray()
            ]);
    }

    /** @test */
    public function it_lists_clients_with_projects()
    {
        $client = Client::factory()->create();

        $projects = Project::factory()->count(3)->create([
            'client_id' => $client->id
        ]);

        $response = $this->json('GET', '/explorer');

        $response->assertStatus(200)
            ->assertJsonPath(
                'data.0.children',
                $projects->map(function ($project) {
                    return [
                        'id' => $project->id,
                        'name' => $project->title,
                        'amount' => 0,
                        'type' => 'project',
                        'children' => []
                    ];
                })->toArray()
            );
    }
}
