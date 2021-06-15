<?php

namespace Database\Factories;

use App\Models\CostType;
use Illuminate\Database\Eloquent\Factories\Factory;

class CostTypeFactory extends Factory
{
    protected $model = CostType::class;

    public function definition()
    {
        return [
            'name' => $this->faker->domainWord,
        ];
    }
}
