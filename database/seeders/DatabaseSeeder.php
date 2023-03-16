<?php

use Database\Seeders\BasisdatenSeeder;
use Database\Seeders\DemoActionSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call([
            BasisdatenSeeder::class,
            DemoActionSeeder::class,
        ]);
    }
}
