<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'youssef',
            'email' => 'youssef@youssef.com',
            'mobile' => '01234567890',
            'password' => Hash::make('admin'),
        ]);
    }
}
