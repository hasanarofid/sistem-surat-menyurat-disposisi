<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuratMasuk>
 */
class SuratMasukFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomor_surat' => 'SM/' . fake()->year() . '/' . fake()->unique()->numberBetween(1000, 9999),
            'tanggal_surat' => fake()->date(),
            'tanggal_terima' => fake()->date(),
            'asal_surat' => fake()->company(),
            'perihal' => fake()->sentence(6),
            'status' => fake()->randomElement(['pending', 'processed', 'archived']),
        ];
    }
}
