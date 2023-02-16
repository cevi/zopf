<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            //
            $table->bigInteger('route_id')->index()->unsigned()->nullable();
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('set null');
            $table->text('content')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            //
            $table->dropForeign('notifications_route_id_foreign');
            $table->dropColumn('route_id');
            $table->dropColumn('content');
        });
    }
};
