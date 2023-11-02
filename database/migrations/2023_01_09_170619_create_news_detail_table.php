<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('news_id')->nullable();
            $table->longText('news_content')->nullable();
            $table->timestamps();
        });
        Schema::table('news_detail', function(Blueprint $table) {
            $table->index('news_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news_detail');
    }
}
