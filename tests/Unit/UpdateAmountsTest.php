<?php

namespace Tests\Unit;

use App\Support\UpdateAmounts;
use PHPUnit\Framework\TestCase;

class UpdateAmountsTest extends TestCase
{
    /** @test */
    public function given_a_complete_client_tree_it_returns_a_tree_with_updated_amounts()
    {
        $input = collect([
            (object)[
                'id' => 1,
                'name' => 'Acme',
                'type' => 'client',
                'amount' => 0,
                'children' => collect([
                    (object)[
                        'id' => 1,
                        'name' => 'New website',
                        'type' => 'project',
                        'amount' => 0,
                        'children' => collect([
                            (object)[
                                'id' => 1,
                                'name' => 'Design',
                                'type' => 'cost',
                                'amount' => 300.00,
                                'children' => collect([
                                    (object)[
                                        'id' => 2,
                                        'name' => 'Web Design',
                                        'type' => 'cost',
                                        'amount' => 200.00,
                                        'children' => collect([])
                                    ],
                                    (object)[
                                        'id' => 3,
                                        'name' => 'Logo Design',
                                        'type' => 'cost',
                                        'amount' => 100.00,
                                        'children' => collect([])
                                    ]
                                ])
                            ],

                            (object)[
                                'id' => 1,
                                'name' => 'Development',
                                'type' => 'cost',
                                'amount' => 800.00,
                                'children' => collect ([
                                    (object) [
                                        'id' => 2,
                                        'name' => 'Web Development',
                                        'type' => 'cost',
                                        'amount' => 200.00,
                                        'children' => collect([])
                                    ]
                                ])
                            ]
                        ])
                    ]
                ])
            ]
        ]);


        $result = (new UpdateAmounts)
            ->execute($input);

        $client = $result->first();
        $project = $client->children->first();
        $design = $project->children->where('name', 'Design')->first();
        $development = $project->children->where('name', 'Development')->first();

        $this->assertEquals(500, $client->amount);
        $this->assertEquals(500, $project->amount);
        $this->assertEquals(300, $design->amount);
        $this->assertEquals(200, $development->amount);
    }
}
