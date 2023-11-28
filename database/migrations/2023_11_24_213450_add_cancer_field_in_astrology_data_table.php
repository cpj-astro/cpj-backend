<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancerFieldInAstrologyDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('astrology_data', function (Blueprint $table) {
            $table->text('cancer')->nullable()->after('gemini');
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
            $table->dropColumn('cancer');
        });
    }
}
