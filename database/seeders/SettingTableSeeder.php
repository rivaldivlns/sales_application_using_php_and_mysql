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
        DB::table('setting')->insert([
            'id_setting' => 1,
            'nama_perusahaan' => 'PT Nusa Indah Jaya Utama',
            'alamat_perusahaan' => 'Jl. Laskar Dalam No.49, RT.003/RW.002, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148',
            'telepon_perusahaan' => '(021) 82411782',
            'email_perusahaan' => 'ptniju@gmail.com',
            'path_logo' => '/img/logo.png'
        ]);
    }
}
