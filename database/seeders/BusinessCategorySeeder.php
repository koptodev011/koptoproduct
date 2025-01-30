<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;  // Import Carbon to work with timestamps

class BusinessCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = Carbon::now(); // Get the current timestamp

        DB::table('business_categories')->insert([
            ['business_category' => 'Accounting & CA Interior Designer', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['business_category' => 'Automobiles/Auto parts', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['business_category' => 'Salon & Spa', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['business_category' => 'Liquor Store', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['business_category' => 'Book / Stationary store', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['business_category' => 'Construction Materials & Equipment', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['business_category' => 'Repairing/ Plumbing/ Electrician', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['business_category' => 'Chemicals & Fertilizers', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['business_category' => 'Computer Equipments & Softwares', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['business_category' => 'Electrical & Electronics Equipments', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['business_category' => 'Fashion Accessory/ Cosmetics', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['business_category' => 'Tailoring/Boutique', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['business_category' => 'Fruit And Vegetable', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['business_category' => 'Kirana/ General Merchant', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['business_category' => 'FMCG Products', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['business_category' => 'Dairy Farm Products/ Poultry', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['business_category' => 'Hardware', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['business_category' => 'Furniture', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ]);
    }
}
