<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder {
    public function run(): void {
        $admin = User::firstOrCreate(
            ['email' => 'admin@school.test'],
            [
                'name' => 'مدير النظام',
                'email' => 'admin@school.test',
                'password' => Hash::make('Admin@1234'),
            ]
        );
        $admin->assignRole('admin');
    }
}
