<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            'Bachelor of Science in Nursing',
            'Master of Arts in Nursing(Masteral)',
            'Bachelor of Science in Psychology',
            'Bachelor of Arts Major in Psychology',
            'Bachelor of Science in Medical Laboratory Science',
            'Bachelor of Science in Physical Therapy',
            'Bachelor of Science in Respiratory Therapy',
            'Bachelor of Science in Pharmacy',
            'Bachelor of Science in Radiologic Technology',
            'Bachelor of Science in Exercise and Sports Sciences',
        ];

        foreach ($courses as $course) {
            Course::create([
                'name' => $course,
            ]);
        }
    }
}
