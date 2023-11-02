<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('private_ads', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('media_file')->nullable();
            $table->enum('ad_type', ['image_banner', 'image_fullscreen', 'video_tv', 'image_tv', 'dashboard_silder','image_listing','video_native','others']);
            $table->tinyInteger('status')->nullable()->default(1);
            $table->string('link')->nullable();
            $table->enum('category', ['app', 'website', 'others']);
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
        Schema::dropIfExists('private_ads');
    }
}
