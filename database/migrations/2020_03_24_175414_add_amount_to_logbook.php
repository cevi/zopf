<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountToLogbook extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logbooks', function (Blueprint $table) {
            //
            $table->integer('quantity');
            $table->boolean('cut');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logbooks', function (Blueprint $table) {
            //
            $table->dropColumn('quantity');
            $table->dropColumn('cut');
        });
    }
}
