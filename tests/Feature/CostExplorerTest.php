<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cost;
use App\Models\Client;
use App\Models\Project;
use App\Models\CostType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

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

        Project::factory()->count(3)->create([
            'client_id' => $client->id
        ]);

        $response = $this->json('GET', '/explorer');

        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data.0.children', 3)
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

        Cost::factory()->create([
            'project_id' => $project->id,
            'cost_type_id' => $costTypeTwo->id,
            'amount' => 100.00,
        ]);

        Cost::factory()->create([
            'project_id' => $project->id,
            'cost_type_id' => $costTypeOne->id,
            'amount' => 300.00,
        ]);

        $response = $this->json('GET', '/explorer');

        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data.0.children.0.children', 2)
        );
    }

    /** @test */
    public function it_lists_child_level_costs()
    {
        $this->withoutExceptionHandling();

        $client = Client::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id
        ]);

        $parent = CostType::factory()->create([
            'name' => 'Design'
        ]);

        $costTypeOne = CostType::factory()->create([
            'name' => 'Web Design',
            'parent_id' => $parent->id
        ]);

        $costTypeTwo = CostType::factory()->create([
            'name' => 'Logo Design',
            'parent_id' => $parent->id
        ]);

        Cost::factory()->create([
            'project_id' => $project->id,
            'cost_type_id' => $parent->id,
            'amount' => 300.00,
        ]);

        Cost::factory()->create([
            'project_id' => $project->id,
            'cost_type_id' => $costTypeOne->id,
            'amount' => 100.00,
        ]);

        Cost::factory()->create([
            'project_id' => $project->id,
            'cost_type_id' => $costTypeTwo->id,
            'amount' => 200.00,
        ]);

        $response = $this->json('GET', '/explorer');

        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data.0.children.0.children', 1)
                ->has('data.0.children.0.children.0.children', 2)
        );
    }
}
