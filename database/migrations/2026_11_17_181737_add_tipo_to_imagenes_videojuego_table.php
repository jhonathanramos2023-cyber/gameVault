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
        Schema::table('imagenes_videojuego', function (Blueprint $table) {
            $table->string('tipo', 20)->default('archivo')->after('orden');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('imagenes_videojuego', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
