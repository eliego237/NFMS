<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@nfms.local'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('Admin@12345'),
            ]
        );

        $admin->assignRole('Administrateur');
    }
}