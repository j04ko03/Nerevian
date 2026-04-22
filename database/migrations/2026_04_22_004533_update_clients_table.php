<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar FK constraint si existeix (SQL Server)
        DB::statement("
            IF EXISTS (
                SELECT 1 FROM sys.foreign_keys
                WHERE name = 'clients_operador_id_foreign'
                  AND parent_object_id = OBJECT_ID('clients')
            )
            ALTER TABLE clients DROP CONSTRAINT clients_operador_id_foreign
        ");

        DB::statement("
            IF EXISTS (
                SELECT 1 FROM sys.columns
                WHERE object_id = OBJECT_ID('clients') AND name = 'operador_id'
            )
            ALTER TABLE clients DROP COLUMN operador_id
        ");

        DB::statement("
            IF NOT EXISTS (
                SELECT 1 FROM sys.columns
                WHERE object_id = OBJECT_ID('clients') AND name = 'actiu'
            )
            ALTER TABLE clients ADD actiu BIT NOT NULL DEFAULT 0
        ");
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedBigInteger('operador_id')->nullable()->after('dni_id');
            $table->dropColumn('actiu');
        });
    }
};