<?php

namespace App\Console\Commands;

use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImportTeacherAccountsCommand extends Command
{
    protected $signature = 'teachers:import-accounts
        {file : Full path to the legacy teachers SQL export}
        {--password= : Password for the imported accounts}
        {--dry-run : Validate the import without changing the database}
        {--reset-password : Reset imported accounts to the configured password}';

    protected $description = 'Import legacy teachers, their assignments, and teacher login accounts';

    /**
     * Stable, readable account names for the teachers in the supplied legacy export.
     * IDs come from the legacy teachers table, not the destination database.
     *
     * @var array<int, string>
     */
    private const USERNAMES = [
        33 => 'shahd_saber',
        34 => 'heba_mohamed',
        35 => 'eman_eid',
        37 => 'jovana_magdy',
        38 => 'asmaa_ismail',
        39 => 'mai_mohamed',
        40 => 'asmaa_amer',
        41 => 'entesar_eldesouky',
        42 => 'omnia_mohamed',
        43 => 'huda_mohamed_ahmed',
        44 => 'aya_hamdy',
        45 => 'islam_elhawary',
        46 => 'mohamed_essam_abdelal',
        47 => 'shaimaa_abdelrahman_elkabir_mohamed',
        48 => 'sherihan_aboueldahab',
        49 => 'basma_mahmoud',
        50 => 'hoor_hossam',
    ];

    /** @var array<string, int> */
    private const GRADE_ORDERS = [
        'kg_1' => 1,
        'kg_2' => 2,
        'primary_1' => 3,
        'primary_2' => 4,
        'primary_3' => 5,
        'primary_4' => 6,
        'primary_5' => 7,
        'primary_6' => 8,
        'prep_1' => 9,
        'prep_2' => 10,
        'prep_3' => 11,
        'sec_1' => 12,
        'sec_2' => 13,
    ];

    public function handle(): int
    {
        $path = (string) $this->argument('file');
        if (! is_file($path) || ! is_readable($path)) {
            $this->error("Cannot read the SQL file: {$path}");

            return self::FAILURE;
        }

        $dump = file_get_contents($path);
        if ($dump === false) {
            $this->error('The SQL file could not be read.');

            return self::FAILURE;
        }

        try {
            $teachers = $this->rowsFromInsert($dump, 'teachers');
            $grades = $this->rowsFromInsert($dump, 'teacher_grades');
        } catch (\RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $gradesByTeacher = collect($grades)->groupBy(fn (array $grade) => (int) $grade[1]);
        $subjectIdsByNormalizedName = Subject::query()
            ->get(['id', 'name', 'name_ar'])
            ->flatMap(fn (Subject $subject) => [
                $this->normalizeName($subject->name) => $subject->id,
                $this->normalizeName($subject->name_ar ?? '') => $subject->id,
            ])
            ->filter()
            ->all();
        $gradeLevelIds = GradeLevel::query()
            ->get(['id', 'order', 'track'])
            ->groupBy(fn (GradeLevel $gradeLevel) => $gradeLevel->order.'|'.(
                $gradeLevel->track instanceof \BackedEnum
                    ? $gradeLevel->track->value
                    : $gradeLevel->track
            ))
            ->map(fn ($levels) => (int) $levels->first()->id)
            ->all();

        $prepared = [];
        $errors = [];

        foreach ($teachers as $row) {
            [$legacyId, $name, $phone, $track, $subjects] = $row;
            $legacyId = (int) $legacyId;
            $username = self::USERNAMES[$legacyId] ?? null;
            if ($username === null) {
                $errors[] = "No email username is configured for legacy teacher #{$legacyId} ({$name}).";
                continue;
            }

            $subjectNames = json_decode((string) $subjects, true, 512, JSON_THROW_ON_ERROR);
            $subjectIds = collect($subjectNames)
                ->map(fn (string $subject) => $subjectIdsByNormalizedName[$this->normalizeName($subject)] ?? null)
                ->filter()
                ->values()
                ->all();
            if (count($subjectIds) !== count($subjectNames)) {
                $missing = collect($subjectNames)
                    ->reject(fn (string $subject) => isset($subjectIdsByNormalizedName[$this->normalizeName($subject)]))
                    ->implode(', ');
                $errors[] = "Teacher {$name}: subject not found in this site ({$missing}).";
            }

            $teacherGradeRows = $gradesByTeacher->get($legacyId, collect());
            $gradeIds = [];
            foreach ($teacherGradeRows as $gradeRow) {
                $order = self::GRADE_ORDERS[$gradeRow[2]] ?? null;
                if ($order === null) {
                    $errors[] = "Teacher {$name}: unknown grade key {$gradeRow[2]}.";
                    continue;
                }

                $tracks = $gradeRow[4] === 'both' ? ['arabic', 'languages'] : [$gradeRow[4]];
                foreach ($tracks as $gradeTrack) {
                    $gradeId = $gradeLevelIds[$order.'|'.$gradeTrack] ?? null;
                    if ($gradeId !== null) {
                        $gradeIds[] = $gradeId;
                    }
                }
            }

            $prepared[] = [
                'legacy_id' => $legacyId,
                'name' => $name,
                'phone' => $phone,
                'track' => $track,
                'email' => $username.'@summit.school',
                'subject_ids' => array_values(array_unique($subjectIds)),
                'grade_level_ids' => array_values(array_unique($gradeIds)),
            ];
        }

        if ($errors !== []) {
            foreach ($errors as $error) {
                $this->error($error);
            }

            $this->error('Nothing was imported. Add the missing subjects or correct the source data, then rerun the command.');

            return self::FAILURE;
        }

        $this->table(
            ['Teacher', 'Email', 'Subjects', 'Grade levels'],
            collect($prepared)->map(fn (array $teacher) => [
                $teacher['name'],
                $teacher['email'],
                count($teacher['subject_ids']),
                count($teacher['grade_level_ids']),
            ])->all()
        );

        if ($this->option('dry-run')) {
            $this->info('Validation complete. No database changes were made.');

            return self::SUCCESS;
        }

        $password = $this->option('password');
        if (! is_string($password) || mb_strlen($password) < 8) {
            $this->error('Supply an account password of at least 8 characters with --password.');

            return self::FAILURE;
        }

        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($prepared, $password, &$created, &$updated): void {
            foreach ($prepared as $data) {
                $user = User::query()->where('email', $data['email'])->first();
                $isNewUser = $user === null;
                if ($isNewUser) {
                    $user = User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => Hash::make($password),
                    ]);
                    $created++;
                } else {
                    $user->update([
                        'name' => $data['name'],
                        'password' => $this->option('reset-password') ? Hash::make($password) : $user->password,
                    ]);
                    $updated++;
                }

                $user->syncRoles(['teacher']);

                $teacher = Teacher::query()->firstOrNew(['user_id' => $user->id]);
                $teacher->fill([
                    'full_name' => $data['name'],
                    'phone' => $data['phone'],
                    'track' => $data['track'],
                ]);
                $teacher->save();
                $teacher->subjects()->sync($data['subject_ids']);
                $teacher->gradeLevels()->sync($data['grade_level_ids']);
            }
        });

        $this->info("Import complete: {$created} accounts created, {$updated} existing accounts updated.");
        $this->line('Every newly created account has the password supplied to this command.');

        return self::SUCCESS;
    }

    /** @return list<list<string|null>> */
    private function rowsFromInsert(string $dump, string $table): array
    {
        $pattern = '/INSERT INTO `'.preg_quote($table, '/').'` \([^\n]+\) VALUES\s*(.*?);/su';
        if (preg_match($pattern, $dump, $match) !== 1) {
            throw new \RuntimeException("No INSERT data for the {$table} table was found.");
        }

        return array_map(fn (string $tuple) => $this->valuesFromTuple($tuple), $this->tuplesFromValues($match[1]));
    }

    /** @return list<string> */
    private function tuplesFromValues(string $values): array
    {
        $tuples = [];
        $buffer = '';
        $depth = 0;
        $quoted = false;
        $escaped = false;

        foreach (str_split($values) as $character) {
            if ($escaped) {
                $buffer .= $character;
                $escaped = false;
                continue;
            }
            if ($quoted && $character === '\\') {
                $buffer .= $character;
                $escaped = true;
                continue;
            }
            if ($character === "'") {
                $quoted = ! $quoted;
            }
            if (! $quoted && $character === '(') {
                $depth++;
                if ($depth === 1) {
                    continue;
                }
            }
            if (! $quoted && $character === ')') {
                $depth--;
                if ($depth === 0) {
                    $tuples[] = $buffer;
                    $buffer = '';
                    continue;
                }
            }
            if ($depth > 0) {
                $buffer .= $character;
            }
        }

        return $tuples;
    }

    /** @return list<string|null> */
    private function valuesFromTuple(string $tuple): array
    {
        $values = [];
        $buffer = '';
        $quoted = false;
        $escaped = false;

        foreach (str_split($tuple) as $character) {
            if ($escaped) {
                $buffer .= $character;
                $escaped = false;
                continue;
            }
            if ($quoted && $character === '\\') {
                $escaped = true;
                continue;
            }
            if ($character === "'") {
                $quoted = ! $quoted;
                continue;
            }
            if (! $quoted && $character === ',') {
                $values[] = $this->normalizeSqlValue($buffer);
                $buffer = '';
                continue;
            }
            $buffer .= $character;
        }
        $values[] = $this->normalizeSqlValue($buffer);

        return $values;
    }

    private function normalizeSqlValue(string $value): ?string
    {
        $value = trim($value);

        return strtoupper($value) === 'NULL' ? null : $value;
    }

    private function normalizeName(string $value): string
    {
        $value = str_replace(['أ', 'إ', 'آ', 'ى', 'ة', 'ـ'], ['ا', 'ا', 'ا', 'ي', 'ه', ''], $value);
        $value = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $value) ?? $value;

        return preg_replace('/\s+/u', ' ', trim($value)) ?? trim($value);
    }
}
