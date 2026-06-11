<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SystemSeeder::class);

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
                'resident_type' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'billing@example.com'],
            [
                'name' => 'Billing Staff',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'billing_staff',
                'resident_type' => null,
            ]
        );
    }
}
