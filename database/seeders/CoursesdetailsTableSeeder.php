<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributesTableSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['type' => 'term', 'value' => 'Spring'],
            ['type' => 'term', 'value' => 'Summer'],
            ['type' => 'term', 'value' => 'Fall'],
            ['type' => 'term', 'value' => 'Winter'],
            ['type' => 'year', 'value' => '2024'],
            ['type' => 'year', 'value' => '2025'],
            ['type' => 'year', 'value' => '2026'],
            ['type' => 'year', 'value' => '2027'],
            ['type' => 'department', 'value' => 'Computer Science and Engineering'],
            ['type' => 'department', 'value' => 'Electrical Engineering'],
            ['type' => 'department', 'value' => 'Mechanical Engineering'],
            ['type' => 'department', 'value' => 'Civil Engineering'],
        ];

        // Insert data and ignore duplicates based on the unique combination of 'type' and 'value'
        DB::table('attributes')->insertOrIgnore($data);
    }
}
