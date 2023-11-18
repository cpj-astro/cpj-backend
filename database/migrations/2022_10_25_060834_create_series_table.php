<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->string('series_id')->nullable();
            $table->string('series_name')->nullable();
            $table->string('series_date')->nullable();
            $table->integer('total_matches')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('image')->nullable();
            $table->string('month_wise')->nullable();
            $table->string('source')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->timestamps();
        });
        Schema::table('series', function(Blueprint $table) {
            $table->index('series_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('series');
    }
}
