<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data_group = [
            ['group_name' => 'root', 'group_desc' => 'administrator', 'group_is_active' => 'yes'],
            ['group_name' => 'user', 'group_desc' => 'normal user', 'group_is_active' => 'yes'],
        ];

        foreach ($data_group as $group) {
            DB::table("sys_group")->insert([
                'group_name' => $group['group_name'],
                'group_desc' => $group['group_desc'],
                'group_is_active' => $group['group_is_active'],
            ]);
        }

        // Insert All Permission to root user
        $data = DB::table("sys_menu_action")->get();
        foreach ($data as $d) {
            DB::table("sys_group_permission")->insert([
                'group_id' => 1,
                'action_id' => $d->action_id,
            ]);
        }
    }
}
