<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('notes')->nullable();
            $table->unsignedTinyInteger('shop_id');
            $table->string('shopify_key')->nullable();
            $table->string('shopify_pass')->nullable();
            $table->string('shopify_hostname')->nullable();
            $table->string('shopify_shared_secret')->nullable();
            $table->string('ebay_appid')->nullable();
            $table->string('ebay_certid')->nullable();
            $table->string('ebay_devid')->nullable();
            $table->string('ebay_runame')->nullable();
            $table->string('ebay_access_token')->nullable();
            $table->string('ebay_refresh_token')->nullable();
            $table->unsignedTinyInteger('active');
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
        Schema::dropIfExists('emails');
    }
}
