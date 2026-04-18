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
        Schema::create('tb_user_area', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('area_id');

            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id_user')
                  ->on('tb_user')
                  ->onDelete('cascade');

            $table->foreign('area_id')
                  ->references('id_area')
                  ->on('tb_area_parkir')
                  ->onDelete('cascade');

            $table->unique(['user_id', 'area_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_user_area');
    }
};
