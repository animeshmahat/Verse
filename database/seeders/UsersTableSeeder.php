<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $createMutipleUsers = [
            ['name' => 'Verse Admin', 'email' => 'admin@gmail.com', 'password' => bcrypt('admin'), 'mobile' => '1234567890', 'role' => 'superadmin', 'username' => 'admin'],
            ['name' => 'Verse Admin', 'email' => 'root@root.com', 'password' => bcrypt('root'), 'mobile' => '6383273344', 'role' => 'superadmin', 'username' => 'admin2'],
            ['name' => 'Verse Admin', 'email' => 'user@user.com', 'password' => bcrypt('user'), 'mobile' => '3323432423', 'role' => 'superadmin', 'username' => 'admin3'],
            ['name' => 'Verse Admin', 'email' => 'sam@sam.com', 'password' => bcrypt('sam'), 'mobile' => '3343434343', 'role' => 'superadmin', 'username' => 'admin4'],
            ['name' => 'Verse Admin', 'email' => 'max@max.com', 'password' => bcrypt('max'), 'mobile' => '4343243243', 'role' => 'superadmin', 'username' => 'admin5']
        ];
        DB::table('users')->insert($createMutipleUsers);
    }
}
