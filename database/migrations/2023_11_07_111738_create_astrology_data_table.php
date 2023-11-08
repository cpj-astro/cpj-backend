<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAstrologyDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('astrology_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pandit_id');
            $table->date('date')->nullable();
            $table->text('aries')->nullable();
            $table->text('taurus')->nullable();
            $table->text('gemini')->nullable();
            $table->text('leo')->nullable();
            $table->text('virgo')->nullable();
            $table->text('libra')->nullable();
            $table->text('scorpio')->nullable();
            $table->text('sagittarius')->nullable();
            $table->text('capricorn')->nullable();
            $table->text('aquarius')->nullable();
            $table->text('pisces')->nullable();
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
        Schema::dropIfExists('astrology_data');
    }
}