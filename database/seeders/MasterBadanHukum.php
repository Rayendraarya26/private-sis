<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterBadanHukum extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = ['PT', "CV", "Koperasi", "Personal"];

        foreach ($data as $d) {
            DB::table('master_badan_hukum')->insert([
                'badan_hukum_nama' => $d,
            ]);
        }
    }
}
