<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->bigInteger('action_id')->index()->unsigned()->nullable();
            $table->bigInteger('user_id')->index()->unsigned()->nullable();
            $table->bigInteger('route_status_id')->index()->unsigned()->nullable();
            $table->timestamps();
        });
        Schema::table('routes', function (Blueprint $table) {
            //
            $table->foreign('action_id')->references('id')->on('actions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('routes', function (Blueprint $table) {
            //
            $table->dropForeign('routes_action_id_foreign');
            $table->dropForeign('routes_user_id_foreign');
        });
        Schema::dropIfExists('routes');
    }
}
