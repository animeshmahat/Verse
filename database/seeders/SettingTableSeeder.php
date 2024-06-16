<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert(
            [
                'site_name' => 'Verse',
                'site_email' => 'mahatanimesh333@gmail.com',
                'site_phone' => '+01 5573254',
                'site_mobile' => '+977-9860606060',
                'site_address' => 'Lalitpur',
                'site_description' => 'Lorem ipsum dolor sit amet.',
                'site_url' => 'http://127.0.0.1:8000/',
                'logo' => NULL
            ]
        );
    }
}
