<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users (id_area 1=MTS, 2=PlazaAraya) ──────────────
        DB::table('tb_user')->insert([
            ['nama_lengkap' => 'Administrator',   'username' => 'admin',    'password' => md5('admin123'),   'role' => 'admin',   'status_aktif' => 1, 'id_area' => null],
        ]);
    }
}
