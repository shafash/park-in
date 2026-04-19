<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tb_user')->insert([
            ['nama_lengkap' => 'Administrator',   'username' => 'admin',    'password' => Hash::make('admin123'),   'role' => 'admin',   'status_aktif' => 1, 'id_area' => null],
        ]);
    }
}
