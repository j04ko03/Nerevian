<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'ofertes')
            CREATE TABLE ofertes (
                id          INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
                solicitud_id INT NOT NULL,
                operador_id  INT NOT NULL,
                preu         DECIMAL(10,2) NOT NULL,
                temps_estimat INT NULL,
                comentaris   NVARCHAR(MAX) NULL,
                estat_oferta_id INT NULL,
                created_at   DATETIME2 NULL,
                updated_at   DATETIME2 NULL,
                CONSTRAINT FK_ofertes_solicitud FOREIGN KEY (solicitud_id) REFERENCES solicitud(id),
                CONSTRAINT FK_ofertes_operador  FOREIGN KEY (operador_id)  REFERENCES usuaris(id)
            )
        ");
    }

    public function down(): void
    {
        DB::statement("
            IF EXISTS (SELECT * FROM sys.tables WHERE name = 'ofertes')
            DROP TABLE ofertes
        ");
    }
};