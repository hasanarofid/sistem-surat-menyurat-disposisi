<?php

namespace Database\Factories;

use App\Models\SuratMasuk;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Disposisi>
 */
class DisposisiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'surat_masuk_id' => SuratMasuk::factory(),
            'pengirim_id' => User::factory(),
            'penerima_id' => User::factory(),
            'instruksi' => fake()->paragraph(),
            'catatan' => fake()->sentence(),
            'status' => fake()->randomElement(['pending', 'completed']),
        ];
    }
}
