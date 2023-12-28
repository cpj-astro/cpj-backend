<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add4fieldsToTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->text('bowler')->nullable();
            $table->text('batsman')->nullable();
            $table->text('wicket_keeper')->nullable();
            $table->text('all_rounder')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('bowler');
            $table->dropColumn('batsman');
            $table->dropColumn('wicket_keeper');
            $table->dropColumn('all_rounder');
        });
    }
}
