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
        if (!Schema::hasColumn('tb_tarif', 'denda_per_jam')) {
            Schema::table('tb_tarif', function (Blueprint $table) {
                $table->integer('denda_per_jam')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tb_tarif', 'denda_per_jam')) {
            Schema::table('tb_tarif', function (Blueprint $table) {
                $table->dropColumn('denda_per_jam');
            });
        }
    }
};
