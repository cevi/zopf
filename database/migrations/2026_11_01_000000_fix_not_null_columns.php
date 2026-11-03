<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Spalten, die der Code beim Anlegen nicht setzt. MySQL mit 'strict' => false speichert still 0 bzw. '',
 * strikte Datenbanken (SQLite in Tests) verweigern das Einfügen.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // War als ->nullable (ohne Klammern) gemeint, siehe create_orders_table.
            $table->integer('sequence')->nullable()->change();
            $table->boolean('pick_up')->default(false)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            // Hitobito-Login legt Benutzer ohne Passwort an.
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('sequence')->change();
            $table->boolean('pick_up')->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->change();
        });
    }
};
