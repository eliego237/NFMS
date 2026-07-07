<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Student::truncate();

        $students = [

            [
                'matricule' => 'NF202600001',
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'gender' => 'M',
                'birth_date' => '2001-05-10',
                'phone' => '699112233',
                'email' => 'jean.dupont@example.com',
                'address' => 'Douala',
                'emergency_contact' => 'Paul Dupont - 677889900',
                'photo' => null,
                'status' => true,
            ],

            [
                'matricule' => 'NF202600002',
                'first_name' => 'Marie',
                'last_name' => 'Ngono',
                'gender' => 'F',
                'birth_date' => '2003-02-18',
                'phone' => '677001122',
                'email' => 'marie.ngono@example.com',
                'address' => 'Yaoundé',
                'emergency_contact' => 'Anne Ngono - 699887766',
                'photo' => null,
                'status' => true,
            ],

        ];

        foreach ($students as $student) {

            Student::create($student);

        }
    }
}