<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = Carbon::now(); // Get the current timestamp

        DB::table('users')->insert([
            [
                'name' => 'Superadmin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('superadmin@123'),
                'role_id' => '1',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]
        ]);
    }
}
