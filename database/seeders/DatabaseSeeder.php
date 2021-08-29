<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(MenuSeeder::class);
        $this->call(GroupSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(NotifSeeder::class);
        $this->call(IconSeeder::class);
        $this->call(MasterEmailSeeder::class);
        $this->call(MasterBadanHukum::class);
        $this->call(MasterProvinceSeeder::class);
        $this->call(MasterRegencySeeder::class);
        $this->call(MasterDistrictSeeder::class);
        $this->call(MasterVillageSeeder::class);
    }
}
