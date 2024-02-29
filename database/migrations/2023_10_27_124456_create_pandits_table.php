<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePanditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pandits', function (Blueprint $table) {
            $table->id(); 
            $table->string('name')->nullable();
            $table->string('avatar_image')->nullable();
            $table->text('description')->nullable();
            $table->integer('experience')->nullable(); 
            $table->float('rating')->nullable();
            $table->integer('match_astrology_price')->nullable();
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
        Schema::dropIfExists('pandits');
    }
}
