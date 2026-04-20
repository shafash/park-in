<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_tarif', function (Blueprint $table) {
            $table->integer('tarif_maks_per_hari')->default(0)->after('tarif_per_jam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_tarif', function (Blueprint $table) {
            $table->dropColumn('tarif_maks_per_hari');
        });
    }
};
