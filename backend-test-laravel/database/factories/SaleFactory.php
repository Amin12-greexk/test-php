<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    public function definition(): array
    {
        // Pastikan Anda memiliki setidaknya satu user di tabel 'users'
        return [
            'user_id' => 1,
            'area_id' => null, // Sesuai dengan database, kolom ini bisa null
        ];
    }
}