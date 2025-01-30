<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = Carbon::now(); // Get the current timestamp

        DB::table('roles')->insert([
            ['role_name' => 'Superadmin', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['role_name' => 'Admin', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['role_name' => 'Staff', 'created_at' => $timestamp, 'updated_at' => $timestamp]
        ]);
    }
}
