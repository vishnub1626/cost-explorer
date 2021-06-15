<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Support\CostTreeBuilder;

class CostTreeBuilderTest extends TestCase
{
    /** @test */
    public function given_an_array_with_elements_it_return_a_tree()
    {
        $input = collect([
            (object)[
                'id' => 1,
                'name' => 'Design',
                'parent_id' => null,
                'amount' => 300
            ],
            (object)[
                'id' => 2,
                'name' => 'Web Design',
                'parent_id' => 1,
                'amount' => 200
            ],
            (object)[
                'id' => 3,
                'name' => 'Logo Design',
                'parent_id' => 1,
                'amount' => 100
            ],
            (object)[
                'id' => 4,
                'name' => 'Development',
                'parent_id' => null,
                'amount' => 300
            ],
            (object)[
                'id' => 5,
                'name' => 'Back End Development',
                'parent_id' => 4,
                'amount' => 200
            ],
        ]);

        $result = (new CostTreeBuilder)
            ->execute($input);

        $this->assertCount(2, $result);

        $this->assertCount(2, $result->where('name', 'Design')->first()->children);
        $this->assertCount(1, $result->where('name', 'Development')->first()->children);
    }
}
