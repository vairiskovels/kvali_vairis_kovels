<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'              => 'John',
            'surname'           => 'Smith',
            'password'          => Hash::make('asd'),
            'currency'          => '€',
            'username'          => 'asd',
            'email'             => 'asd@asd.com',
            'admin'             => 1
        ]);
    }
}
