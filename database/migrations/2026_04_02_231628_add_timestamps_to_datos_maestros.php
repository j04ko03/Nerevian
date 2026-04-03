<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Añadimos timestamps a la tabla Puertos
        Schema::table('ports', function (Blueprint $table) {
            $table->timestamps(); // Esto crea created_at y updated_at
        });

        // Añadimos timestamps a la tabla Incoterms
        Schema::table('tipus_incoterms', function (Blueprint $table) {
            $table->timestamps();
        });

        // Añadimos timestamps a la tabla Paissos
        Schema::table('paissos', function (Blueprint $table) {
            $table->timestamps();
        });

        // Añadimos timestamps a la tabla Ciutats
        Schema::table('ciutats', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    public function down()
    {
        // Si algún día revertimos la migración, borramos las columnas
        Schema::table('puertos', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('incoterms', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('paissos', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('ciutats', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};