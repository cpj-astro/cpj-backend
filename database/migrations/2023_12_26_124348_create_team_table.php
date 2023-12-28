<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id')->nullable();
            $table->text('team_name')->nullable();
            $table->text('p1')->nullable();
            $table->text('p2')->nullable();
            $table->text('p3')->nullable();
            $table->text('p4')->nullable();
            $table->text('p5')->nullable();
            $table->text('p6')->nullable();
            $table->text('p7')->nullable();
            $table->text('p8')->nullable();
            $table->text('p9')->nullable();
            $table->text('p10')->nullable();
            $table->text('p11')->nullable();
            $table->text('captain')->nullable();
            $table->text('vice_captain')->nullable();
            $table->tinyInteger('status')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teams');
    }
}
