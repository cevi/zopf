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
            $table->integer('action_status_id')->index()->unsigned()->nullable();
            $table->timestamps();
        });
        Schema::table('actions', function (Blueprint $table) {    
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actions', function (Blueprint $table) {
            $table->dropForeign('actions_group_id_foreign');
        });

        Schema::dropIfExists('actions');
    }
}
