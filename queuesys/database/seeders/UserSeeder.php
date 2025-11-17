<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Office;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an Admin
        User::create([
            'name' => 'admin',
            'email' => 'queue_admin@example.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'office_id' => null,
        ]);
    }
}
