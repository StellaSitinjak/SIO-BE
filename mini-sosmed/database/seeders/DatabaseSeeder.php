<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::create([
            'name'     => 'Super Admin',
            'email'    => 'superadmin@gmail.com',
            'password' => Hash::make('superadmin123'),
        ]);

        \App\Models\UserDetail::create([
            'user_id'       => 1,
            'phone_number'  => 1,
            'image'         => null,
            'username'      => 'superadmin',
            'first_name'    => 'super',
            'last_name'     => 'admin',
            'date_of_birth' => '2023-12-15',
        ]);
    }
}
