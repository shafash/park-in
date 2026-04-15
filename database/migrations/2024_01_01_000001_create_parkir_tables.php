<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_user', function (Blueprint $table) {
            $table->increments('id_user');
            $table->string('nama_lengkap', 50);
            $table->string('username', 50)->unique();
            $table->string('password', 100);
            $table->enum('role', ['admin', 'petugas', 'owner']);
            $table->tinyInteger('status_aktif')->default(1);
            $table->unsignedInteger('id_area')->nullable(); // ditambah FK setelah area_parkir dibuat
        });

        Schema::create('tb_tarif', function (Blueprint $table) {
            $table->increments('id_tarif');
            $table->enum('jenis_kendaraan', ['motor', 'mobil', 'lainnya']);
            $table->decimal('tarif_per_jam', 10, 0);
        });

        Schema::create('tb_area_parkir', function (Blueprint $table) {
            $table->increments('id_area');
            $table->string('nama_area', 50);
            $table->string('alamat', 100)->default('');
            $table->integer('kapasitas')->default(0);
            $table->integer('terisi')->default(0);
            $table->tinyInteger('status')->default(1);
        });

        // Tambahkan FK id_area ke tb_user setelah tb_area_parkir dibuat
        Schema::table('tb_user', function (Blueprint $table) {
            $table->foreign('id_area')->references('id_area')->on('tb_area_parkir')->nullOnDelete();
        });

        Schema::create('tb_kendaraan', function (Blueprint $table) {
            $table->increments('id_kendaraan');
            $table->string('plat_nomor', 15)->unique();
            $table->string('jenis_kendaraan', 20);
            $table->string('merek', 50)->default('');
            $table->string('warna', 20)->default('');
            $table->string('pemilik', 100)->default('');
            $table->string('foto', 255)->default('');
            $table->unsignedInteger('id_user')->nullable();
            $table->datetime('created_at')->useCurrent();
            $table->foreign('id_user')->references('id_user')->on('tb_user')->nullOnDelete();
        });

        Schema::create('tb_transaksi', function (Blueprint $table) {
            $table->increments('id_parkir');
            $table->unsignedInteger('id_kendaraan');
            $table->datetime('waktu_masuk');
            $table->datetime('waktu_keluar')->nullable();
            $table->unsignedInteger('id_tarif');
            $table->integer('durasi_jam')->default(0);
            $table->decimal('biaya_total', 10, 0)->default(0);
            $table->enum('status', ['masuk', 'keluar'])->default('masuk');
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_area');
            $table->foreign('id_kendaraan')->references('id_kendaraan')->on('tb_kendaraan');
            $table->foreign('id_tarif')->references('id_tarif')->on('tb_tarif');
            $table->foreign('id_user')->references('id_user')->on('tb_user');
            $table->foreign('id_area')->references('id_area')->on('tb_area_parkir');
        });

        Schema::create('tb_log_aktivitas', function (Blueprint $table) {
            $table->increments('id_log');
            $table->unsignedInteger('id_user');
            $table->string('aktivitas', 100);
            $table->datetime('waktu_aktivitas')->useCurrent();
            $table->foreign('id_user')->references('id_user')->on('tb_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_log_aktivitas');
        Schema::dropIfExists('tb_transaksi');
        Schema::dropIfExists('tb_kendaraan');
        Schema::dropIfExists('tb_area_parkir');
        Schema::dropIfExists('tb_tarif');
        Schema::dropIfExists('tb_user');
    }
};
