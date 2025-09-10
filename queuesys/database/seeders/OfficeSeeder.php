<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Office;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offices = [
            'Business Office',
            'Library',
            'Student Affairs',
            'Registrar',
        ];

        foreach ($offices as $name) {
            Office::create(['name' => $name]);
        }
    }
}
