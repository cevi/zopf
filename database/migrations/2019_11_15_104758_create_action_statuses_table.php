<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_statuses', function (Blueprint $table) {
            $table->Increments('id');
            $table->string('name');
            $table->timestamps();
        });
        DB::table('action_statuses')->insert( 
            array(
                ['id' => 5, 'name' => 'Aktiv'],
                ['id' => 10, 'name' => 'Abgeschlossen']
            )
        );
    }
    //INSERT INTO `action_statuses` (`id`, `name`, `created_at`, `updated_at`) VALUES ('5', 'Aktiv', NULL, NULL), ('10', 'Abgeschlossen', NULL, NULL);

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('action_statuses');
    }
}
