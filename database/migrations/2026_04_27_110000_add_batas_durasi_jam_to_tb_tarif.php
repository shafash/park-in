<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tb_tarif', 'batas_durasi_jam')) {
            Schema::table('tb_tarif', function (Blueprint $table) {
                $table->integer('batas_durasi_jam')->default(8);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tb_tarif', 'batas_durasi_jam')) {
            Schema::table('tb_tarif', function (Blueprint $table) {
                $table->dropColumn('batas_durasi_jam');
            });
        }
    }
};
