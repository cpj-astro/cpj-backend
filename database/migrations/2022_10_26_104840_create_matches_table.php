<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('series_id')->nullable();
            $table->string('match_id')->nullable();
            $table->string('date_wise')->nullable();
            $table->string('match_date')->nullable();
            $table->string('match_time')->nullable();
            $table->string('matchs')->nullable();
            $table->string('venue')->nullable();
            $table->string('match_type', 20)->nullable();
            $table->string('result')->nullable();
            $table->decimal('min_rate', 8, 2)->nullable();
            $table->decimal('max_rate', 8, 2)->nullable();
            $table->string('fav_team', 20)->nullable();
            $table->integer('team_a_id')->nullable();
            $table->string('team_a')->nullable();
            $table->string('team_a_short', 20)->nullable();
            $table->string('team_a_img')->nullable();
            $table->string('team_a_scores', 20)->nullable();
            $table->decimal('team_a_over', 4, 2)->nullable();
            $table->json('team_a_score')->nullable();
            $table->integer('team_b_id')->nullable();
            $table->string('team_b')->nullable();
            $table->string('team_b_short', 20)->nullable();
            $table->string('team_b_img')->nullable();
            $table->string('team_b_scores', 20)->nullable();
            $table->decimal('team_b_over', 4, 2)->nullable();
            $table->json('team_b_score')->nullable();
            $table->string('s_ovr')->nullable();
            $table->string('s_min')->nullable();
            $table->string('s_max')->nullable();
            $table->string('session')->nullable();
            $table->string('toss')->nullable();
            $table->string('umpire')->nullable();
            $table->string('third_umpire')->nullable();
            $table->string('referee')->nullable();
            $table->string('man_of_match')->nullable();
            $table->enum('match_category', ['live', 'recent', 'upcoming']);
            $table->string('source')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->timestamps();
        });

        Schema::table('matches', function(Blueprint $table) {
            $table->index('series_id');
            $table->index('match_id');
            $table->index('match_category');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matches');
    }
}
