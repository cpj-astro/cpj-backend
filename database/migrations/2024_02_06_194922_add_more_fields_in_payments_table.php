<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFieldsInPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->text('merchant_transaction_id')->after('pandit_id')->nullable();
            $table->text('transaction_id')->after('merchant_transaction_id')->nullable();
            $table->text('payment_instrument')->after('transaction_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('merchant_transaction_id');
            $table->dropColumn('transaction_id');
            $table->dropColumn('payment_instrument');
        });
    }
}
