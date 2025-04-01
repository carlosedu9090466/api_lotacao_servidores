<?php

namespace Database\Seeders;

use App\Models\User;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
   
    public function run()
    {
        User::create([
            'name' => 'Carlos Admin',
            'email' => 'carlosadm@gmail.com',
            'password' => Hash::make('@123@123'),
        ]);
    }
}
