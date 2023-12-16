<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAstrologyStatusFieldInMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->enum('astrology_status', ['enable', 'disable'])->default('disable')->after('match_type');
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
            $table->dropColumn('astrology_status');
        });
    }
}
