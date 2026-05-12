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
        Schema::table('notification', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->string('titol')->nullable()->after('user_id');
            $table->text('missatge')->nullable()->after('titol');
            $table->boolean('llegida')->default(false)->after('missatge');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'titol', 'missatge', 'llegida', 'created_at', 'updated_at']);
        });
    }
};
