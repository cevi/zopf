<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRouteStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
        DB::table('route_statuses')->insert( 
            array(
                ['id' => 5, 'name' => 'Geplant'],
                ['id' => 10, 'name' => 'Vorbereitet'],
                ['id' => 15, 'name' => 'Unterwegs'],
                ['id' => 20, 'name' => 'Abgeschlossen'],
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
        Schema::dropIfExists('route_statuses');
    }
}
