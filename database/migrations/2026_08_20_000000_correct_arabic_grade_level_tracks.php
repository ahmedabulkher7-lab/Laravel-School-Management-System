<?php

use App\Enums\StudyTrack;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Correct legacy Arabic grade levels that were accidentally classified as Languages.
     */
    public function up(): void
    {
        DB::table('grade_levels')
            ->where('track', StudyTrack::Languages->value)
            ->where('name', 'like', '%(عربي)%')
            ->update(['track' => StudyTrack::Arabic->value]);
    }

    public function down(): void
    {
        // The previous value was invalid for the affected grade levels.
    }
};
