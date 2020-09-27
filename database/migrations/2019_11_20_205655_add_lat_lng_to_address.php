<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLatLngToAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addresses', function (Blueprint $table) {
            //
            $table->float('lat', 8, 6);
            $table->float('lng', 8, 6);
            $table->string('city');
            $table->integer('plz');
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
            //
            $table->dropColumn('lat');
            $table->dropColumn('lng');
            $table->dropColumn('city');
            $table->dropColumn('plz');
        });
    }
}
