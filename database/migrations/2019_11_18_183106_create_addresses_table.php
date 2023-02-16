<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('firstname');
            $table->string('street');
            $table->bigInteger('city_id')->index()->unsigned()->nullable();
            $table->bigInteger('group_id')->index()->unsigned()->nullable();
            $table->float('lat', 8, 6);
            $table->float('lng', 8, 6);
            $table->string('city');
            $table->integer('plz');
            $table->boolean('center')->default(false);
            $table->timestamps();
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->foreign('city_id')->references('id')->on('cities');
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
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign('addresses_city_id_foreign');
            $table->dropForeign('addresses_group_id_foreign');
        });
        Schema::dropIfExists('addresses');
    }
}
