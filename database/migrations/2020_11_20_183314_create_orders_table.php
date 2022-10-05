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
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('set null');
            $table->bigInteger('action_id')->index()->unsigned()->nullable();
            $table->foreign('action_id')->references('id')->on('actions')->onDelete('cascade');
            $table->bigInteger('address_id')->index()->unsigned()->nullable();
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
            $table->bigInteger('order_status_id')->index()->unsigned()->nullable();
            $table->foreign('order_status_id')->references('id')->on('order_statuses');
            $table->boolean('pick_up');
            $table->integer('sequence')->nullable;
            $table->string('comments')->nullable();
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
