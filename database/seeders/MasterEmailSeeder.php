<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MasterEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create('id_ID');

        DB::table('master_email_template')
            ->insert([
                'template_uuid' => Str::uuid(),
                'template_code' => "GREETING",
                'template_desc' => "Contoh master template email greeting, tes untuk mengirim dengan url {HOSTNAME}/email/schedule/send-greeting",
                'template_mail_subject' => "Informasi untuk {FULLNAME}",
                'template_mail_body' => '<p>Halo {FULLNAME} email kamu {EMAIL} | {EMAIL}' . $faker->paragraph . '</p><br>' . "<p>" . $faker->paragraph . " {FULLNAME}</p>",
            ]);
    }
}
