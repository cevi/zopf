<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('is_admin')->default(0);
            $table->timestamps();
        });
    }
    //INSERT INTO `roles` (`id`, `name`, `is_admin`, `created_at`, `updated_at`) VALUES (NULL, 'Admin', '1', NULL, NULL), (NULL, 'Logistik-Chef', '1', NULL, NULL)
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
