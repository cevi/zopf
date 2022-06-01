<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBakeryProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bakery_progress', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('action_id')->index()->unsigned();
            $table->bigInteger('user_id')->index()->unsigned();
            $table->Time('when');
            $table->integer('raw_material')->default(0);
            $table->integer('dough')->default(0);
            $table->integer('braided')->default(0);
            $table->integer('baked')->default(0);
            $table->integer('delivered')->default(0);
            $table->integer('total')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bakery_progress');
    }
}
