<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTaxGroupSeeder extends Seeder
{
    public function run()
    {
        DB::table('producttaxgroups')->insert([
            'product_tax_group' => 'General tax group',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
