<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKundliColumnsInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('birth_date')->after('password')->nullable();
            $table->time('birth_time')->after('birth_date')->nullable();
            $table->string('birth_place')->after('birth_time')->nullable();
            $table->string('latitude')->after('birth_place')->nullable();
            $table->string('longitude')->after('latitude')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('birth_date');
            $table->dropColumn('birth_time');
            $table->dropColumn('birth_place');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
    }
}
