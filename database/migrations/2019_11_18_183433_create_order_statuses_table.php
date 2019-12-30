<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
        DB::table('order_statuses')->insert( 
            array(
                ['id' => 5, 'name' => 'Offen'],
                ['id' => 10, 'name' => 'Unterwegs'],
                ['id' => 20, 'name' => 'Ausgeliefert'],
                ['id' => 25, 'name' => 'Hinterlegt']
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
        Schema::dropIfExists('order_statuses');
    }
}
