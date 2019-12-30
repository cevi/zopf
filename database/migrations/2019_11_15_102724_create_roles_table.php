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
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_groupleader')->default(false);
            $table->timestamps();
        });
        DB::table('roles')->insert(
            array(
                ['name' => 'Administrator', 'is_admin' => true, 'is_groupleader' => false],
                ['name' => 'Gruppen Chef', 'is_admin' => false, 'is_campleader' => true],
                ['name' => 'Leiter', 'is_admin' => false, 'is_campleader' => false],
            )
        );
    }
    //INSERT INTO `roles` (`id`, `name`, `is_admin`, `is_groupleader`, `created_at`, `updated_at`) VALUES (NULL, 'Administrator', '1', '0', NULL, NULL), (NULL, 'Gruppen Chef', '0', '1', NULL, NULL), (NULL, 'Leiter', '0', '0', NULL, NULL);
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
