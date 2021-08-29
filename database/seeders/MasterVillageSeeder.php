<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterVillageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataProvinces = fopen(storage_path("db_migration/villages.csv"), 'r');
        $loop = 1;
        $now = Carbon::now();
        while (($data = fgetcsv($dataProvinces, 0, ",")) !== FALSE) {
            if ($loop > 1) {
                DB::table('master_desa')
                    ->insert([
                        "des_id" => $data[0],
                        "kec_id" => $data[1],
                        "des_nama" => $data[2],
                        "created_at" => $now
                    ]);
            }
            $loop++;
        }
    }
}
