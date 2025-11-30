<?php

namespace Database\Seeders;

use App\Models\Hold;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HoldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Hold::factory()->count(1)->create();
    }
}
