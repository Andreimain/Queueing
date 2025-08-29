<?php

namespace Database\Factories;

use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitorFactory extends Factory
{
    protected $model = Visitor::class;

    public function definition()
    {
        $offices = ['Business Office', 'Library', 'Student Affairs', 'Registrar'];
        $office = $this->faker->randomElement($offices);

        // Get today's date
        $today = now()->toDateString();

        // Find latest queue number for this office today
        $maxQueueNumber = Visitor::where('office', $office)
            ->whereDate('created_at', $today)
            ->max('queue_number');

        $nextNumber = $maxQueueNumber ? $maxQueueNumber + 1 : 1;

        return [
            'first_name'   => $this->faker->firstName,
            'last_name'    => $this->faker->lastName,
            'contact'      => $this->faker->phoneNumber,
            'email'        => $this->faker->safeEmail,
            'office'       => $office,
            'queue_number' => $nextNumber,
            'status'       => 'waiting',
            'created_at'   => now(),
            'updated_at'   => now(),
        ];
    }
}
