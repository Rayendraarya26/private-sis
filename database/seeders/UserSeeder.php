<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['fullname' => 'Aldino Kemal', 'email' => 'kemal@mailinator.com', 'password' => '2104'],
            ['fullname' => 'Joko Widodo', 'email' => 'joko@mailinator.com', 'password' => '2104'],
        ];

        foreach ($data as $d) {
            DB::table("sys_user")->insert([
                'user_fullname' => $d['fullname'],
                'user_email' => $d['email'],
                'user_password' => bcrypt($d['password']),
                'user_is_active' => 'yes',
                'user_active_at' => date("Y-m-d H:i:s"),
                'user_picture' => "/images/profiles/default.png",
            ]);

            DB::table('sys_user_group')->insert([
                'ug_user_id' => DB::getPdo()->lastInsertId(),
                'ug_group_id' => 1,
                'ug_is_default' => 'yes',
            ]);
        }
    }
}
