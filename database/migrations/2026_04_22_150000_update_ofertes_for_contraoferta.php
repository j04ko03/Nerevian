<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            IF NOT EXISTS (
                SELECT 1 FROM sys.columns
                WHERE object_id = OBJECT_ID('ofertes') AND name = 'es_contraoferta'
            )
            ALTER TABLE ofertes ADD es_contraoferta BIT NOT NULL DEFAULT 0
        ");
    }

    public function down(): void
    {
        DB::statement("
            IF EXISTS (
                SELECT 1 FROM sys.columns
                WHERE object_id = OBJECT_ID('ofertes') AND name = 'es_contraoferta'
            )
            ALTER TABLE ofertes DROP COLUMN es_contraoferta
        ");
    }
};