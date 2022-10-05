<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('year');
            $table->bigInteger('group_id')->index()->unsigned()->nullable();
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->integer('action_status_id')->index()->unsigned()->nullable();
            $table->foreign('action_status_id')->references('id')->on('action_statuses');
            $table->bigInteger('address_id')->index()->unsigned()->nullable();
            $table->foreign('address_id')->references('id')->on('addresses');
            $table->string('APIKey');
            $table->string('SmartsuppToken')->nullable();
            $table->boolean('global')->default(false);
            $table->boolean('demo')->default(false);
            $table->integer('total_amount')->nullable();
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
        Schema::dropIfExists('actions');
    }
}
