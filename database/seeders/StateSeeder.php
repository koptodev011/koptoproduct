<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = Carbon::now(); // Get the current timestamp

        DB::table('states')->insert([
            ['state' => 'Andhra Pradesh', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Arunachal Pradesh', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Assam', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Bihar', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Chhattisgarh', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Goa', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Gujarat', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Haryana', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Himachal Pradesh', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Jharkhand', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Karnataka', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Kerala', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Madhya Pradesh', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Maharashtra', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Manipur', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Meghalaya', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Mizoram', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Nagaland', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Odisha', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Punjab', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Rajasthan', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Sikkim', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Tamil Nadu', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Telangana', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Tripura', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Uttar Pradesh', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Uttarakhand', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'West Bengal', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Andaman and Nicobar Islands', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Chandigarh', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Dadra and Nagar Haveli and Daman and Diu', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Lakshadweep', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Delhi', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['state' => 'Puducherry', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ]);
    }
}
