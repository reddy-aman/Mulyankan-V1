<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a specific test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Call additional seeders here, like RolesSeeder
        $this->call([
            RolesSeeder::class,
            CoursesdetailsTableSeeder::class,  // Ensure this is correctly called
        ]);
    }
}
