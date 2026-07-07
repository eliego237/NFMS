<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $methods = [

            [
                'name' => 'Espèces',
                'code' => 'CASH',
            ],

            [
                'name' => 'Orange Money',
                'code' => 'OM',
            ],

            [
                'name' => 'MTN Mobile Money',
                'code' => 'MOMO',
            ],

            [
                'name' => 'Carte bancaire',
                'code' => 'CARD',
            ],

            [
                'name' => 'Virement bancaire',
                'code' => 'BANK',
            ],

        ];

        foreach ($methods as $method) {

            PaymentMethod::firstOrCreate(
                ['code' => $method['code']],
                [
                    'name' => $method['name'],
                    'is_active' => true,
                ]
            );

        }
    }
}