<?php

namespace Database\Seeders;

use App\Models\GradeLevel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use LogicException;
use Spatie\Permission\Models\Role;

class StudentRosterSeeder extends Seeder
{
    /** @var list<string> */
    private const STUDENT_NAMES = [
        'أحمد محمد', 'محمد أحمد', 'محمود حسن', 'عمر خالد', 'يوسف أحمد',
        'ياسين محمد', 'كريم محمود', 'مصطفى أحمد', 'عبدالله محمد', 'عبدالرحمن أحمد',
        'إبراهيم محمد', 'إسلام محمود', 'عمرو حسن', 'خالد محمد', 'حسن أحمد',
        'حسين محمود', 'زياد أحمد', 'أدهم محمد', 'سيف خالد', 'سيف أحمد',
        'معاذ محمد', 'أنس محمود', 'حازم حسن', 'شريف أحمد', 'طارق محمد',
        'إياد أحمد', 'مروان حسن', 'تامر محمد', 'وليد أحمد', 'سامح محمود',
        'حسام حسن', 'رامي محمد', 'مازن أحمد', 'حمدي محمود', 'فادي حسن',
        'باسم محمد', 'إيهاب أحمد', 'علاء محمود', 'أشرف حسن', 'وائل محمد',
        'هاني أحمد', 'شادي محمود', 'نبيل حسن', 'رائد محمد', 'فارس أحمد',
        'سليم محمود', 'يحيى حسن', 'زياد محمد', 'مراد أحمد', 'جاد محمود',
        'آية محمد', 'مريم أحمد', 'نور محمد', 'سارة أحمد', 'منة الله محمد',
        'ملك أحمد', 'هنا محمود', 'جنى محمد', 'جنا أحمد', 'بسملة محمد',
        'حبيبة أحمد', 'سلمى محمود', 'ريم محمد', 'رنا أحمد', 'ندى محمد',
        'ياسمين أحمد', 'بسنت محمود', 'دعاء محمد', 'أسماء أحمد', 'آلاء محمد',
        'فرح أحمد', 'شهد محمود', 'رحمة محمد', 'روضة أحمد', 'مروة محمد',
        'مي أحمد', 'دينا محمود', 'منار محمد', 'شيماء أحمد', 'إسراء محمد',
        'نادين أحمد', 'نجلاء محمود', 'هاجر محمد', 'رضوى أحمد', 'سمر محمد',
        'سارة محمود', 'ريهام أحمد', 'إيمان محمد', 'إنجي أحمد', 'دنيا محمود',
        'بسمة محمد', 'علا أحمد', 'حنان محمد', 'دعاء محمود', 'رقية أحمد',
        'صفاء محمد', 'لينا أحمد', 'ليلى محمود', 'كارما محمد', 'ملك محمد',
    ];

    public function run(): void
    {
        if (count(self::STUDENT_NAMES) !== 100 || count(array_unique(self::STUDENT_NAMES)) !== 100) {
            throw new LogicException('Student roster must contain exactly 100 unique names.');
        }

        DB::transaction(function (): void {
            $gradeLevels = GradeLevel::query()
                ->withCount('students')
                ->orderBy('track')
                ->orderBy('order')
                ->lockForUpdate()
                ->get();

            if ($gradeLevels->isEmpty()) {
                throw new LogicException('Create grade levels before seeding the student roster.');
            }

            $unseededStudents = collect(self::STUDENT_NAMES)
                ->values()
                ->filter(fn (string $name, int $index): bool => ! Student::query()
                    ->whereHas('user', fn ($query) => $query->where('email', $this->emailFor($index + 1)))
                    ->exists())
                ->values()
                ->all();

            if ($unseededStudents === []) {
                $this->command?->info('The 100 named students are already present.');

                return;
            }

            $requiredSlots = $gradeLevels
                ->sum(fn (GradeLevel $gradeLevel): int => max(0, 5 - $gradeLevel->students_count));

            if ($requiredSlots > count($unseededStudents)) {
                throw new LogicException(sprintf(
                    'At least %d additional students are required to give every grade five students; only %d roster entries remain.',
                    $requiredSlots,
                    count($unseededStudents),
                ));
            }

            /** @var list<GradeLevel> $assignments */
            $assignments = [];

            foreach ($gradeLevels as $gradeLevel) {
                for ($slot = $gradeLevel->students_count; $slot < 5; $slot++) {
                    $assignments[] = $gradeLevel;
                }
            }

            $gradeIndex = 0;
            while (count($assignments) < count($unseededStudents)) {
                $assignments[] = $gradeLevels[$gradeIndex % $gradeLevels->count()];
                $gradeIndex++;
            }

            $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

            foreach ($unseededStudents as $offset => $fullName) {
                $rosterNumber = array_search($fullName, self::STUDENT_NAMES, true) + 1;
                $gradeLevel = $assignments[$offset];

                $user = User::firstOrCreate(
                    ['email' => $this->emailFor($rosterNumber)],
                    [
                        'name' => $fullName,
                        'password' => Hash::make('password'),
                        'email_verified_at' => now(),
                    ],
                );
                $user->assignRole($studentRole);

                Student::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'grade_level_id' => $gradeLevel->id,
                        'track' => $gradeLevel->track?->value,
                        'full_name' => $fullName,
                        'date_of_birth' => now()->subYears($gradeLevel->order + 4)->subDays($rosterNumber)->toDateString(),
                        'guardian_name' => "ولي أمر {$fullName}",
                        'guardian_phone' => sprintf('010%08d', $rosterNumber),
                        'enrollment_date' => now()->startOfYear()->toDateString(),
                    ],
                );
            }

            $this->command?->info(sprintf('%d named students added.', count($unseededStudents)));
        });
    }

    private function emailFor(int $rosterNumber): string
    {
        return sprintf('roster-student-%03d@school.local', $rosterNumber);
    }
}
