<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class userseeds extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::create([
            'name' => 'Ngurah Tenaya',
            'id_desas' => '15',
            'email' => 'NgurahTenaya@example.com',
            'status' => 'actived',
            'role' => 'penguji',
            'password'  => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Ade Junior',
            'id_desas' => '15',
            'email' => 'test1@example.com',
            'status' => 'actived',
            'role' => 'admin',
            'password'  => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Ari Senior',
            'id_desas' => '15',
            'email' => 'test2@example.com',
            'status' => 'actived',
            'role' => 'admin',
            'password'  => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Ari Junior',
            'id_desas' => '15',
            'email' => 'test3@example.com',
            'status' => 'actived',
            'role' => 'users',
            'password'  => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Budhi Senior',
            'id_desas' => '15',
            'email' => 'test4@example.com',
            'status' => 'actived',
            'role' => 'users',
            'password'  => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Budhi Junior',
            'id_desas' => '45',
            'email' => 'test5@example.com',
            'status' => 'actived',
            'role' => 'users',
            'password'  => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Hrida',
            'id_desas' => '45',
            'email' => 'test6@example.com',
            'status' => 'actived',
            'role' => 'users',
            'password'  => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Ngurah ',
            'id_desas' => '41',
            'email' => 'test7@example.com',
            'status' => 'actived',
            'role' => 'users',
            'password'  => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Gita ',
            'id_desas' => '32',
            'email' => 'test8@example.com',
            'status' => 'actived',
            'role' => 'users',
            'password'  => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Wahyu Aditya ',
            'id_desas' => '32',
            'email' => 'test9@example.com',
            'status' => 'actived',
            'role' => 'admin',
            'password'  => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Wahyu Aditya ',
            'id_desas' => '15',
            'email' => 'test0@example.com',
            'status' => 'actived',
            'role' => 'admin',
            'password'  => Hash::make('password123'),
        ]);
    }
}
