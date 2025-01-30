<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('business_types')->insert([
            [
                'business_type' => 'Retail',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type' => 'Wholesale',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type' => 'Distributer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type' => 'Service',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type' => 'Manufacturing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more business types as needed
        ]);
    }
}
