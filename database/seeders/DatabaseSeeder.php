<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);
        $this->call(AspekSeeder::class);
        $this->call(IndikatorPersepsiSeeder::class);
        $this->call(BobotAspekSeeder::class);
        $this->call(PesanWaSeeder::class);
    }
}
