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
                ['id' => 5, 'name' => 'Zu Fuss', 'travelmode' => 'WALKING'],
                ['id' => 10, 'name' => 'Fahrrad', 'travelmode' => 'BICYCLING'],
                ['id' => 15, 'name' => 'Auto', 'travelmode' => 'DRIVING'],
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
