<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'Cliente demo',
            'email' => 'cliente@example.com',
        ]);

        $this->call(PizzaSeeder::class);
    }
}
