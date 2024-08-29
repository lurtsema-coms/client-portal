<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Test Admin',
            'company_cell_number' => '123-456-789',
            'company_address' => '123 Street Cubao',
            'email' => 'test@admin.com',
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Test Client',
            'role' => 'client',
            'client_type' => 'business',
            'company_cell_number' => '123-456-789',
            'company_address' => '123 Street Cubao',
            'email' => 'test@client.com',
        ]);
    }
}
