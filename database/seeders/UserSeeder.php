<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->where('email', 'generalcricketfastestuser@gmail.com')->delete();
        DB::table('users')->insert([
            'name' => 'General User',
            'email' => 'generalcricketfastestuser@gmail.com',
            'password' => Hash::make('2hZYn4G8E%tL%gUp34ra'),
            'status' => 1,
            'user_type' => 1
        ]);
    }
}
