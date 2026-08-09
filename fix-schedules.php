<?php
$file = 'resources/views/admin/schedules/create.blade.php';
$content = file_get_contents($file);

$replacement = '
                <div class="form-group">
                    <label class="form-label">المعلم <span style="color:#ef4444">*</span></label>
                    <select name="teacher_id" class="form-select" required>
                        <option value="">-- اختر المعلم --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old(\'teacher_id\') == $teacher->id ? \'selected\' : \'\' }}>
                                {{ $teacher->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error(\'teacher_id\') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">المادة <span style="color:#ef4444">*</span></label>
                    <select name="subject_id" class="form-select" required>
                        <option value="">-- اختر المادة --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old(\'subject_id\') == $subject->id ? \'selected\' : \'\' }}>
                                {{ $subject->name_ar ?? $subject->name }}
                            </option>
                        @endforeach
                    </select>
                    @error(\'subject_id\') <span class="form-error">{{ $message }}</span> @enderror
                </div>
';

$content = preg_replace('/<div class="form-group">\s*<label class="form-label">المعلم والمادة.*?<input type="hidden" name="subject_id" id="subject_id" value="{{ old\(\'subject_id\'\) }}">/s', $replacement, $content);
$content = preg_replace('/<script>\s*function updateSubjects\(\).*?<\/script>/s', '', $content);

file_put_contents($file, $content);

$file = 'resources/views/admin/schedules/edit.blade.php';
$content = file_get_contents($file);

$replacement2 = '
                <div class="form-group">
                    <label class="form-label">المعلم <span style="color:#ef4444">*</span></label>
                    <select name="teacher_id" class="form-select" required>
                        <option value="">-- اختر المعلم --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old(\'teacher_id\', $schedule->teacher_id) == $teacher->id ? \'selected\' : \'\' }}>
                                {{ $teacher->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error(\'teacher_id\') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">المادة <span style="color:#ef4444">*</span></label>
                    <select name="subject_id" class="form-select" required>
                        <option value="">-- اختر المادة --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old(\'subject_id\', $schedule->subject_id) == $subject->id ? \'selected\' : \'\' }}>
                                {{ $subject->name_ar ?? $subject->name }}
                            </option>
                        @endforeach
                    </select>
                    @error(\'subject_id\') <span class="form-error">{{ $message }}</span> @enderror
                </div>
';

$content = preg_replace('/<div class="form-group">\s*<label class="form-label">المعلم والمادة.*?<input type="hidden" name="subject_id" id="subject_id" value="{{ old\(\'subject_id\', \$schedule->subject_id\) }}">/s', $replacement2, $content);
$content = preg_replace('/<script>\s*function updateSubjects\(\).*?<\/script>/s', '', $content);

file_put_contents($file, $content);
echo "Done";
