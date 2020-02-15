<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRouteTypeToRoute extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('routes', function (Blueprint $table) {
            //
            $table->bigInteger('route_type_id')->index()->unsigned()->nullable();
            $table->foreign('route_type_id')->references('id')->on('route_types'); 
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
            $table->dropForeign('routes_route_type_id_foreign');
            $table->dropColumn('route_type_id');
        });
    }
}
