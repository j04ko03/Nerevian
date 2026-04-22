<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar totes les FKs sobre operador_id (nom real: FK_solicitud_usuaris)
        DB::statement("
            DECLARE @sql NVARCHAR(MAX) = ''
            SELECT @sql += 'ALTER TABLE solicitud DROP CONSTRAINT ' + name + '; '
            FROM sys.foreign_keys
            WHERE parent_object_id = OBJECT_ID('solicitud')
              AND name IN (
                SELECT fk.name
                FROM sys.foreign_keys fk
                JOIN sys.foreign_key_columns fkc ON fk.object_id = fkc.constraint_object_id
                JOIN sys.columns c ON fkc.parent_object_id = c.object_id
                    AND fkc.parent_column_id = c.column_id
                WHERE fk.parent_object_id = OBJECT_ID('solicitud')
                  AND c.name = 'operador_id'
              )
            EXEC sp_executesql @sql
        ");

        DB::statement("ALTER TABLE solicitud ALTER COLUMN operador_id INT NULL");

        DB::statement("
            ALTER TABLE solicitud
                ADD CONSTRAINT FK_solicitud_usuaris
                FOREIGN KEY (operador_id) REFERENCES usuaris(id)
        ");
    }

    public function down(): void
    {
        DB::statement("
            IF EXISTS (
                SELECT 1 FROM sys.foreign_keys
                WHERE name = 'FK_solicitud_usuaris'
                  AND parent_object_id = OBJECT_ID('solicitud')
            )
            ALTER TABLE solicitud DROP CONSTRAINT FK_solicitud_usuaris
        ");

        DB::statement("ALTER TABLE solicitud ALTER COLUMN operador_id INT NOT NULL");

        DB::statement("
            ALTER TABLE solicitud
                ADD CONSTRAINT FK_solicitud_usuaris
                FOREIGN KEY (operador_id) REFERENCES usuaris(id)
        ");
    }
};