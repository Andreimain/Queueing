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
            'email' => 'queue_admin@lorma.edu',
            'password' => Hash::make('sysd3v'),
            'role' => 'admin',
            'office_id' => null,
        ]);
    }
}
