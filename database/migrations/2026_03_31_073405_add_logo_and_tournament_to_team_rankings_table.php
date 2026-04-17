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
        Schema::table('team_rankings', function (Blueprint $table) {
            $table->string('team_logo')->nullable()->after('team_name');
            $table->string('tournament_name')->nullable()->after('game_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_rankings', function (Blueprint $table) {
            $table->dropColumn(['team_logo', 'tournament_name']);
        });
    }
};
