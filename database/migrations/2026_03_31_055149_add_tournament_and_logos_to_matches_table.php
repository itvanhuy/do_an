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
        Schema::table('matches', function (Blueprint $table) {
            $table->string('tournament_name')->nullable()->after('game_type');
            $table->string('team1_logo')->nullable()->after('team1_name');
            $table->string('team2_logo')->nullable()->after('team2_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn(['tournament_name', 'team1_logo', 'team2_logo']);
        });
    }
};
