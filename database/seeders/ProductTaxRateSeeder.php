<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTaxRateSeeder extends Seeder
{
    public function run()
    {
        // Fetch the "General tax group" ID from the producttaxgroups table
        $taxGroup = DB::table('producttaxgroups')->where('product_tax_group', 'General tax group')->first();

        // Insert tax rate data
        DB::table('producttaxrates')->insert([
            [
                'product_tax_name' => 'IGST@0%',
                'product_tax_rate' => 0.000,
                'product_tax_group_id' => $taxGroup ? $taxGroup->id : null, // Assign tax group ID
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_tax_name' => 'GST@0%',
                'product_tax_rate' => 0.000,
                'product_tax_group_id' => $taxGroup ? $taxGroup->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_tax_name' => 'IGST@0.25%',
                'product_tax_rate' => 0.025,
                'product_tax_group_id' => $taxGroup ? $taxGroup->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_tax_name' => 'IGST@0.25%',
                'product_tax_rate' => 0.025,
                'product_tax_group_id' => $taxGroup ? $taxGroup->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_tax_name' => 'GST@0.25%',
                'product_tax_rate' => 0.025,
                'product_tax_group_id' => $taxGroup ? $taxGroup->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_tax_name' => 'IGST@3%',
                'product_tax_rate' => 3.000,
                'product_tax_group_id' => $taxGroup ? $taxGroup->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_tax_name' => 'GST@3%',
                'product_tax_rate' => 3.000,
                'product_tax_group_id' => $taxGroup ? $taxGroup->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_tax_name' => 'IGST@5%',
                'product_tax_rate' => 5.000,
                'product_tax_group_id' => $taxGroup ? $taxGroup->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_tax_name' => 'GST@5%',
                'product_tax_rate' => 5.000,
                'product_tax_group_id' => $taxGroup ? $taxGroup->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_tax_name' => 'IGST@12%',
                'product_tax_rate' => 12.000,
                'product_tax_group_id' => $taxGroup ? $taxGroup->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_tax_name' => 'GST@12%',
                'product_tax_rate' => 12.000,
                'product_tax_group_id' => $taxGroup ? $taxGroup->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_tax_name' => 'IGST@18%',
                'product_tax_rate' => 18.000,
                'product_tax_group_id' => $taxGroup ? $taxGroup->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_tax_name' => 'GST@18%',
                'product_tax_rate' => 18.000,
                'product_tax_group_id' => $taxGroup ? $taxGroup->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_tax_name' => 'IGST@28%',
                'product_tax_rate' => 28.000,
                'product_tax_group_id' => $taxGroup ? $taxGroup->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_tax_name' => 'GST@28%',
                'product_tax_rate' => 28.000,
                'product_tax_group_id' => $taxGroup ? $taxGroup->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
