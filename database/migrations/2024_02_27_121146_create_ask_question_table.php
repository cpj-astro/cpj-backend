<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAskQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ask_question', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('wtsp_number');
            $table->text('question');
            $table->text('answer')->nullable();
            $table->boolean('is_wtsp_number')->default(false);
            $table->boolean('status')->default(true);
            $table->text('transaction_id')->nullable();
            $table->text('merchant_transaction_id')->nullable();
            $table->text('amount')->nullable();
            $table->text('payment_instrument')->nullable();
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
        Schema::dropIfExists('ask_question');
    }
}
