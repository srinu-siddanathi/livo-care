<?php

namespace Database\Seeders;

use App\Models\Test;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // Truncate the tests table to remove all existing records
        DB::table('tests')->truncate();

        // Read JSON file
        $json = File::get(base_path('lab_tests.json'));
        $tests = json_decode($json, true);

        // Insert new records from JSON
        foreach ($tests as $test) {
            Test::create([
                'name' => $test['name'],
                'code' => $test['code'],
                'description' => $test['description'] ?? null,
                'volume' => $test['vollume'] ?? null,
                'price' => $test['price'],
            ]);
        }

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }
} 