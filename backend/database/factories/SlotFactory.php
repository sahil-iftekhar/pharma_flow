<?php

namespace Database\Factories;

use App\Models\Pharmacist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Slot>
 */
class SlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start_time = fake()->time('H:i:s');
        $end_time = date('H:i:s', strtotime($start_time) + 3600); // Add one hour

        return [
            'pharmacist_id' => Pharmacist::factory(),
            'date' => fake()->date(),
            'start_time' => $start_time,
            'end_time' => $end_time,
            'is_available' => fake()->boolean(),
        ];
    }
}