<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peticions_registre', function (Blueprint $table) {
            // tinyint(1) es tratado como boolean por MySQL y trunca valores >1 a 1.
            // Lo convertimos a unsignedTinyInteger para que pueda almacenar 0, 1 y 2.
            $table->unsignedTinyInteger('estat')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('peticions_registre', function (Blueprint $table) {
            $table->boolean('estat')->default(0)->change();
        });
    }
};