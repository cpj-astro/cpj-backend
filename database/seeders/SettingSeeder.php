<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;


class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::truncate();
        Setting::create([
            'id'            => 1,
            'setting_key'   => 'tv_setting',
            'setting_value' => json_encode(['tv_url'=>''])
        ]);
        Setting::create([
            'id'            => 2,
            'setting_key'   => 'current_app_version',
            'setting_value' => '0'
        ]);
    }
}
