<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create('id_ID');

        for ($i = 0; $i <= 50; $i++):
            DB::table('sys_user_notif')
                ->insert([
                    'notif_user_id' => 1,
                    'notif_title' => $faker->sentence,
                    'notif_content' => $faker->paragraph,
                    'notif_link' => $faker->url,
                    'notif_created_at' => $faker->dateTime,
                ]);
        endfor;
    }
}
