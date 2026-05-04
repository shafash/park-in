<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah Index Krusial untuk Performa (Mencegah Full Table Scan)
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->index(['waktu_masuk', 'status'], 'idx_waktu_status');
            $table->index('waktu_keluar', 'idx_waktu_keluar');
        });

        Schema::table('tb_kendaraan', function (Blueprint $table) {
            $table->index('plat_nomor', 'idx_plat_nomor');
        });

        // 2. Ubah ENUM menjadi VARCHAR (Agar scalable)
        // Note: For MySQL modifying ENUM to VARCHAR requires raw statement
        DB::statement("ALTER TABLE tb_tarif MODIFY jenis_kendaraan VARCHAR(50)");
        DB::statement("ALTER TABLE tb_kendaraan MODIFY jenis_kendaraan VARCHAR(50)");

        // 3. Tambah SoftDeletes untuk Data Integrity
        Schema::table('tb_tarif', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('tb_area_parkir', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->dropIndex('idx_waktu_status');
            $table->dropIndex('idx_waktu_keluar');
        });
        Schema::table('tb_kendaraan', function (Blueprint $table) {
            $table->dropIndex('idx_plat_nomor');
        });
        
        Schema::table('tb_tarif', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('tb_area_parkir', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
