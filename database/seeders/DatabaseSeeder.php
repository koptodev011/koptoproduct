<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ProductCategory;
use App\Models\BusinessTypes;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(ProductCategorySeeder::class);
        $this->call(BusinessTypesSeeder::class);
        $this->call(StateSeeder::class);
        $this->call(ProductBaseUnitSeeder::class);
        $this->call(BusinessCategorySeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(ProductTaxGroupSeeder::class);
        $this->call(ProductTaxRateSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CitySeeder::class);
        $this->call(SalesPaymentTypeSeeder::class);
    }
}
