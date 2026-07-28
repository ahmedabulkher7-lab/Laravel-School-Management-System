<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GradeLevel;

class GradeLevelSeeder extends Seeder {
    public function run(): void {
        $levels = [
            ['name' => 'KG1', 'order' => 1],
            ['name' => 'KG2', 'order' => 2],
            ['name' => 'الصف الأول', 'order' => 3],
            ['name' => 'الصف الثاني', 'order' => 4],
            ['name' => 'الصف الثالث', 'order' => 5],
            ['name' => 'الصف الرابع', 'order' => 6],
            ['name' => 'الصف الخامس', 'order' => 7],
            ['name' => 'الصف السادس', 'order' => 8],
            ['name' => 'الصف السابع', 'order' => 9],
            ['name' => 'الصف الثامن', 'order' => 10],
            ['name' => 'الصف التاسع', 'order' => 11],
            ['name' => 'الصف العاشر', 'order' => 12],
            ['name' => 'الصف الحادي عشر', 'order' => 13],
        ];
        foreach ($levels as $level) {
            GradeLevel::firstOrCreate(['order' => $level['order']], $level);
        }
    }
}
