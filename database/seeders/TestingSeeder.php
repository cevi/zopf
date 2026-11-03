<?php

namespace Database\Seeders;

use App\Models\City;

/**
 * Stammdaten für Tests: wie BasisdatenSeeder, aber ohne den Ortschaften-Import aus storage/app/cities.sql.
 */
class TestingSeeder extends BasisdatenSeeder
{
    protected function seedCities()
    {
        City::create(['plz' => 8000, 'name' => 'Zürich']);
        City::create(['plz' => 3000, 'name' => 'Bern']);
    }
}
