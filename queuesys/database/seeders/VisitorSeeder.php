<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Visitor;

class VisitorSeeder extends Seeder
{
    public function run(): void
    {
        $offices = [
            'Business Office',
            'Library',
            'Student Affairs',
            'Registrar',
        ];

        foreach ($offices as $office) {
            // Get the current max queue number for this office
            $lastQueueNumber = Visitor::where('office', $office)->max('queue_number') ?? 0;

            // Generate 20 new visitors continuing from last number
            for ($i = 1; $i <= 20; $i++) {
                Visitor::factory()->create([
                    'office'       => $office,
                    'queue_number' => $lastQueueNumber + $i,
                ]);
            }
        }
    }
}
