<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([
            RoleSeeder::class,
            GradeLevelSeeder::class,
            AdminUserSeeder::class,
            FakeDataSeeder::class,
            WeeklyPlanSeeder::class,
        ]);
    }
}
