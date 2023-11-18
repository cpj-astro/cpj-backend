<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFewColumnsToMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->decimal('back1',6,2)->after('man_of_match')->nullable();
            $table->decimal('back2',6,2)->after('back1')->nullable();
            $table->decimal('back3',6,2)->after('back2')->nullable();
            $table->decimal('lay1',6,2)->after('back3')->nullable();
            $table->decimal('lay2',6,2)->after('lay1')->nullable();
            $table->decimal('lay3',6,2)->after('lay2')->nullable();
            $table->text('batsman')->after('lay3')->nullable();
            $table->string('bowler')->after('batsman')->nullable();
            $table->decimal('curr_rate',5,2)->after('bowler')->nullable();
            $table->text('first_circle')->after('curr_rate')->nullable();
            $table->text('fancy')->after('first_circle')->nullable();
            $table->text('last4overs')->after('fancy')->nullable();
            $table->text('lastwicket')->after('last4overs')->nullable();
            $table->decimal('match_over', 4,2)->after('lastwicket')->nullable();
            $table->string('partnership')->after('match_over')->nullable();
            $table->decimal('rr_rate', 5,2)->after('partnership')->nullable();
            $table->string('second_circle')->after('rr_rate')->nullable();
            $table->smallInteger('target')->after('second_circle')->nullable();
            $table->string('team_a_scores_over')->after('team_a_score')->nullable();
            $table->string('team_b_scores_over')->after('team_b_score')->nullable();
            $table->text('yet_to_bat')->after('target')->nullable();
            $table->integer('balling_team')->after('yet_to_bat')->nullable();
            $table->integer('batting_team')->after('balling_team')->nullable();
            $table->string('current_inning',20)->after('batting_team')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn('back1');
            $table->dropColumn('back2');
            $table->dropColumn('back3');
            $table->dropColumn('lay1');
            $table->dropColumn('lay2');
            $table->dropColumn('lay3');
            $table->dropColumn('batsman');
            $table->dropColumn('bowler');
            $table->dropColumn('curr_rate');
            $table->dropColumn('first_circle');
            $table->dropColumn('fancy');
            $table->dropColumn('last4overs');
            $table->dropColumn('lastwicket');
            $table->dropColumn('match_over');
            $table->dropColumn('partnership');
            $table->dropColumn('rr_rate');
            $table->dropColumn('second_circle');
            $table->dropColumn('target');
            $table->dropColumn('team_a_scores_over');
            $table->dropColumn('team_b_scores_over');
            $table->dropColumn('yet_to_bat');
            $table->dropColumn('balling_team');
            $table->dropColumn('batting_team');
            $table->dropColumn('current_inning');
        });
    }
}
