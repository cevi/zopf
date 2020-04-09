<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->bigIncrements('id');
            $table->integer('quantity');
            $table->bigInteger('route_id')->index()->unsigned()->nullable();
            $table->bigInteger('action_id')->index()->unsigned()->nullable();
            $table->bigInteger('address_id')->index()->unsigned()->nullable();
            $table->bigInteger('order_status_id')->index()->unsigned()->nullable();
            $table->boolean('pick_up');
            $table->timestamps();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('action_id')->references('id')->on('actions')->onDelete('cascade');  
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->dropForeign('orders_action_id_foreign');
            $table->dropForeign('orders_address_id_foreign');
        });
        Schema::dropIfExists('orders');
    }
}
