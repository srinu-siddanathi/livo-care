<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DiagnosticCenter;
use App\Models\Doctor;
use App\Models\Test;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create Doctors
        $doctors = [
            [
                'name' => 'Dr. John Doe',
                'email' => 'doctor@example.com',
                'password' => Hash::make('password123'),
                'specialization' => 'Cardiologist',
                'phone' => '1234567890'
            ],
            [
                'name' => 'Dr. Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password123'),
                'specialization' => 'Neurologist',
                'phone' => '9876543210'
            ],
            [
                'name' => 'Dr. Robert Wilson',
                'email' => 'robert@example.com',
                'password' => Hash::make('password123'),
                'specialization' => 'Pediatrician',
                'phone' => '5555555555'
            ]
        ];

        foreach ($doctors as $doctor) {
            Doctor::create($doctor);
        }

        // Create Diagnostic Centers
        $centers = [
            [
                'name' => 'City Diagnostics',
                'email' => 'city@example.com',
                'password' => Hash::make('password123'),
                'address' => '123 Main St, City',
                'phone' => '1111111111'
            ],
            [
                'name' => 'HealthCare Labs',
                'email' => 'healthcare@example.com',
                'password' => Hash::make('password123'),
                'address' => '456 Park Ave, Town',
                'phone' => '2222222222'
            ],
            [
                'name' => 'Metro Diagnostics',
                'email' => 'metro@example.com',
                'password' => Hash::make('password123'),
                'address' => '789 Metro Rd, City',
                'phone' => '3333333333'
            ]
        ];

        foreach ($centers as $center) {
            DiagnosticCenter::create($center);
        }

        // Create Medical Tests
        $tests = [
            [
                'name' => 'Complete Blood Count (CBC)',
                'description' => 'Measures different components of blood including red cells, white cells, and platelets'
            ],
            [
                'name' => 'Blood Sugar Test',
                'description' => 'Measures glucose levels in blood to diagnose diabetes'
            ],
            [
                'name' => 'Lipid Profile',
                'description' => 'Measures cholesterol and triglycerides in blood'
            ],
            [
                'name' => 'Thyroid Function Test',
                'description' => 'Measures thyroid hormones to check thyroid function'
            ],
            [
                'name' => 'Liver Function Test',
                'description' => 'Measures various proteins and enzymes to check liver health'
            ],
            [
                'name' => 'Kidney Function Test',
                'description' => 'Measures creatinine and other markers for kidney function'
            ],
            [
                'name' => 'X-Ray Chest',
                'description' => 'Imaging test to examine bones and organs in chest'
            ],
            [
                'name' => 'ECG',
                'description' => 'Records electrical activity of heart'
            ],
            [
                'name' => 'Ultrasound',
                'description' => 'Imaging test using sound waves'
            ],
            [
                'name' => 'MRI Scan',
                'description' => 'Detailed imaging using magnetic fields and radio waves'
            ]
        ];

        foreach ($tests as $test) {
            Test::create($test);
        }
    }
}
