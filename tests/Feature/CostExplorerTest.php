<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cost;
use App\Models\Client;
use App\Models\Project;
use App\Models\CostType;
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

    /** @test */
    public function it_lists_root_level_project_costs()
    {
        $this->withoutExceptionHandling();

        $client = Client::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id
        ]);

        $costTypeOne = CostType::factory()->create([
            'name' => 'Design'
        ]);

        $costTypeTwo = CostType::factory()->create([
            'name' => 'Development'
        ]);

        $costOne = Cost::factory()->create([
            'project_id' => $project->id,
            'cost_type_id' => $costTypeTwo->id,
            'amount' => 100.00,
        ]);

        $costTwo = Cost::factory()->create([
            'project_id' => $project->id,
            'cost_type_id' => $costTypeOne->id,
            'amount' => 300.00,
        ]);

        $response = $this->json('GET', '/explorer');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.children.0.children', [
                [
                    'id' => $costTypeTwo->id,
                    'name' => $costTypeTwo->name,
                    'amount' => 100,
                    'type' => 'cost',
                    'children' => []
                ],
                [
                    'id' => $costTypeOne->id,
                    'name' => $costTypeOne->name,
                    'amount' => 300,
                    'type' => 'cost',
                    'children' => []
                ]
            ]);
    }
}
