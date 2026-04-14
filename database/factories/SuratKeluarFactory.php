<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuratKeluar>
 */
class SuratKeluarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomor_surat' => 'SK/' . fake()->year() . '/' . fake()->unique()->numberBetween(1000, 9999),
            'tanggal_surat' => fake()->date(),
            'tujuan_surat' => fake()->company(),
            'perihal' => fake()->sentence(6),
            'status' => fake()->randomElement(['draft', 'sent']),
        ];
    }
}
