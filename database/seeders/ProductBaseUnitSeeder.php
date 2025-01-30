<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductBaseUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = Carbon::now(); // Get the current timestamp

        DB::table('productbaseunits')->insert([
            ['product_base_unit' => 'BAGS (Bag)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'BOTTLES (Btl)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'BOX (Box)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'BUNDLES (Bdl)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'CANS (Can)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'CARTONS (Ctn)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'DOZENS (Dzn)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'GRAMMES (Gm) ', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'KILOGRAMS (Kg)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'LITRE (Ltr)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => ' METERS (Mtr)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => ' MILILITRE (MI)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => ' NUMBERS (Nos)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'PACKS (Pac)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'PAIRS (Prs)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'PIECES (Pcs)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'QUINTAL (Qtl)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'ROLLS (Rol)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'SQUARE FEET (Sqf)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'SQUARE METERS (Sqm)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'TABLES (Tbl)', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ]);
    
    }
}
