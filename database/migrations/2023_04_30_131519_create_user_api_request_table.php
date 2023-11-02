<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserApiRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_api_request', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('request_counter')->nullable();
            $table->string('request_ip', 50)->nullable();
            $table->string('request_token')->nullable();
            $table->timestamps();
        });
        Schema::table('user_api_request', function(Blueprint $table) {
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_api_request');
    }
}
