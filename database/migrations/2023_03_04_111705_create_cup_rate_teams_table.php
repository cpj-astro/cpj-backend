<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCupRateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cup_rate_teams', function (Blueprint $table) {
            $table->id();
            $table->integer('cup_rate_id')->nullable();
            $table->string('team_name')->nullable();
            $table->string('back', 20)->nullable();
            $table->string('lay', 20)->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->timestamps();
        });
        Schema::table('cup_rate_teams', function(Blueprint $table) {
            $table->index('cup_rate_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cup_rate_teams');
    }
}
