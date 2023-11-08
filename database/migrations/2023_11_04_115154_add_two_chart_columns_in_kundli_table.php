<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwoChartColumnsInKundliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kundli', function (Blueprint $table) {
            $table->string('horoscope_svg')->after('kundli_data')->nullable();
            $table->string('navamsa_svg')->after('horoscope_svg')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kundli', function (Blueprint $table) {
            $table->dropColumn('horoscope_svg');
            $table->dropColumn('navamsa_svg');
        });
    }
}
