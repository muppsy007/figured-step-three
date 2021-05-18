<?php

use Illuminate\Database\Seeder;
use Database\Seeders\InventorySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seeder for populating tables using Figured excercise data
        $this->call(InventorySeeder::class);

    }
}
