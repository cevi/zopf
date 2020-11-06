<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRouteTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('name');
            $table->string('travelmode');
        });
        DB::table('route_types')->insert( 
            array(
                ['id' => config('status.route_type_walking'), 'name' => 'Zu Fuss', 'travelmode' => 'WALKING'],
                ['id' => config('status.route_type_cycling'), 'name' => 'Fahrrad', 'travelmode' => 'BICYCLING'],
                ['id' => config('status.route_type_driving'), 'name' => 'Auto', 'travelmode' => 'DRIVING'],
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_types');
    }
}
