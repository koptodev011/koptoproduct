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
            ['product_base_unit' => 'BAGS (Bag)', 'shortname' => 'Bag', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'BOTTLES (Btl)', 'shortname' => 'Btl', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'BOX (Box)', 'shortname' => 'Box', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'BUNDLES (Bdl)', 'shortname' => 'Bdl', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'CANS (Can)', 'shortname' => 'Can', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'CARTONS (Ctn)', 'shortname' => 'Ctn', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'DOZENS (Dzn)', 'shortname' => 'Dzn', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'GRAMMES (Gm)', 'shortname' => 'Gm', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'KILOGRAMS (Kg)', 'shortname' => 'Kg', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'LITRE (Ltr)', 'shortname' => 'Ltr', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'METERS (Mtr)', 'shortname' => 'Mtr', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'MILILITRE (Ml)', 'shortname' => 'Ml', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'NUMBERS (Nos)', 'shortname' => 'Nos', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'PACKS (Pac)', 'shortname' => 'Pac', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'PAIRS (Prs)', 'shortname' => 'Prs', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'PIECES (Pcs)', 'shortname' => 'Pcs', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'QUINTAL (Qtl)', 'shortname' => 'Qtl', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'ROLLS (Rol)', 'shortname' => 'Rol', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'SQUARE FEET (Sqf)', 'shortname' => 'Sqf', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'SQUARE METERS (Sqm)', 'shortname' => 'Sqm', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['product_base_unit' => 'TABLES (Tbl)', 'shortname' => 'Tbl', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ]);
    }
}
