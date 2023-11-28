<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditAstrologyDataTableAddMatchId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('astrology_data', function (Blueprint $table) {            
            // Add the new 'match_id' column
            $table->unsignedBigInteger('match_id')->after('pandit_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('astrology_data', function (Blueprint $table) {
            // Reverse the changes made in the 'up' method
            $table->dropColumn('match_id');
        });
    }
}
