<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('mailacc');
            $table->unsignedTinyInteger('shop_id');
            $table->string('buyer_name');
            $table->string('buyer_username')->nullable();
            $table->string('buyer_email');
            $table->text('buyer_address');
            $table->string('item_name');
            $table->float('price', 8, 2);
            $table->unsignedTinyInteger('qty');
            $table->string('tracking');
            $table->string('tracking_status');
            $table->string('order_status');
            $table->string('payment_status');
            $table->Integer('user_id');
            $table->Integer('shop_order_id');
            $table->string('shop_order_status')->nullable();
            $table->string('shop_payment_status');
            $table->string('notes')->nullable();
            $table->timestamp('order_at');
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
        Schema::dropIfExists('orders');
    }
}
