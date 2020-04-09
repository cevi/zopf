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
                ['id' => config('status.order_offen'), 'name' => 'Offen'],
                ['id' => config('status.order_unterwegs'), 'name' => 'Unterwegs'],
                ['id' => config('status.order_ausgeliefert'), 'name' => 'Ausgeliefert'],
                ['id' => config('status.order_hinterlegt'), 'name' => 'Hinterlegt'],
                ['id' => config('status.order_abgeholt'), 'name' => 'Abgeholt']
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
