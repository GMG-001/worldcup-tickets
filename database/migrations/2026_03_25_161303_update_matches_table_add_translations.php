<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn(['home_team', 'away_team', 'stadium']);

            $table->string('home_team_en')->after('id');
            $table->string('home_team_ka')->after('home_team_en');
            $table->string('away_team_en')->after('home_team_ka');
            $table->string('away_team_ka')->after('away_team_en');
            $table->string('stadium_en')->after('away_team_ka');
            $table->string('stadium_ka')->after('stadium_en');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn([
                'home_team_en', 'home_team_ka',
                'away_team_en', 'away_team_ka',
                'stadium_en',   'stadium_ka',
            ]);

            $table->string('home_team')->after('id');
            $table->string('away_team')->after('home_team');
            $table->string('stadium')->after('away_team');
        });
    }
};
