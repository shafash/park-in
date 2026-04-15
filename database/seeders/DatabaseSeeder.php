<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Tarif ────────────────────────────────────────────
        DB::table('tb_tarif')->insert([
            ['jenis_kendaraan' => 'mobil',   'tarif_per_jam' => 3000],
            ['jenis_kendaraan' => 'motor',   'tarif_per_jam' => 2000],
            ['jenis_kendaraan' => 'lainnya', 'tarif_per_jam' => 8000],
        ]);

        // ── Area Parkir ───────────────────────────────────────
        DB::table('tb_area_parkir')->insert([
            ['nama_area' => 'Malang Town Square', 'alamat' => 'Jl. Veteran No.2',       'kapasitas' => 120, 'terisi' => 0, 'status' => 1],
            ['nama_area' => 'Plaza Araya',         'alamat' => 'Jl. Mayjen Sungkono',    'kapasitas' => 80,  'terisi' => 0, 'status' => 1],
            ['nama_area' => 'DP Mall Malang',      'alamat' => 'Jl. Jaksa Agung No. 23', 'kapasitas' => 60,  'terisi' => 0, 'status' => 0],
            ['nama_area' => 'Alun-Alun Malang',    'alamat' => 'Jl. Merdeka Selatan',    'kapasitas' => 50,  'terisi' => 0, 'status' => 1],
        ]);

        // ── Users (id_area 1=MTS, 2=PlazaAraya) ──────────────
        DB::table('tb_user')->insert([
            ['nama_lengkap' => 'Administrator',   'username' => 'admin',    'password' => md5('admin123'),   'role' => 'admin',   'status_aktif' => 1, 'id_area' => null],
            ['nama_lengkap' => 'Milena Maverick',  'username' => 'petugas',  'password' => md5('petugas123'), 'role' => 'petugas', 'status_aktif' => 1, 'id_area' => 1],
            ['nama_lengkap' => 'Leonel Maverick',  'username' => 'owner',    'password' => md5('owner123'),   'role' => 'owner',   'status_aktif' => 1, 'id_area' => null],
            ['nama_lengkap' => 'Rama Dhanuarta',   'username' => 'petugas2', 'password' => md5('petugas123'), 'role' => 'petugas', 'status_aktif' => 0, 'id_area' => 1],
            ['nama_lengkap' => 'Ethaniel Vanjiro', 'username' => 'petugas3', 'password' => md5('petugas123'), 'role' => 'petugas', 'status_aktif' => 1, 'id_area' => 2],
        ]);

        // ── Kendaraan (dengan merek & foto kosong) ────────────
        DB::table('tb_kendaraan')->insert([
            ['plat_nomor' => 'D 5678 ABC',  'jenis_kendaraan' => 'motor',   'merek' => 'Honda Vario 160',    'warna' => 'Merah',  'pemilik' => 'Keizuro Isaac',    'foto' => '', 'id_user' => null, 'created_at' => '2020-03-17 08:00:00'],
            ['plat_nomor' => 'B 123 XYZ',   'jenis_kendaraan' => 'mobil',   'merek' => 'Toyota Avanza',      'warna' => 'Putih',  'pemilik' => 'Aurora Morheo',    'foto' => '', 'id_user' => null, 'created_at' => '2020-03-15 08:00:00'],
            ['plat_nomor' => 'L 9012 DEF',  'jenis_kendaraan' => 'mobil',   'merek' => 'Honda Brio',         'warna' => 'Hitam',  'pemilik' => 'Iris Amara P.',    'foto' => '', 'id_user' => null, 'created_at' => '2020-03-12 08:00:00'],
            ['plat_nomor' => 'AA 3456 GHI', 'jenis_kendaraan' => 'lainnya', 'merek' => 'Isuzu Elf',          'warna' => 'Kuning', 'pemilik' => 'Arthaniel Kenjiro','foto' => '', 'id_user' => null, 'created_at' => '2020-03-09 08:00:00'],
            ['plat_nomor' => 'BK 7890 JKL', 'jenis_kendaraan' => 'motor',   'merek' => 'Yamaha R15',         'warna' => 'Biru',   'pemilik' => 'Emilia',           'foto' => '', 'id_user' => null, 'created_at' => '2020-02-23 08:00:00'],
        ]);
    }
}
