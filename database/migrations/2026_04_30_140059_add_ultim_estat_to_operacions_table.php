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
        Schema::table('operacions', function (Blueprint $table) {
            $table->string('ultim_estat')->nullable()->after('estat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operacions', function (Blueprint $table) {
            $table->dropColumn('ultim_estat');
        });
    }
};
