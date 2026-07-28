<div class="glass" style="padding:1.5rem;margin-bottom:1.25rem;transition:box-shadow 0.2s;">

    @if($saved)
        <div class="alert-success" style="margin-bottom:1rem;animation:fadeIn 0.3s ease;">
            <i class="fas fa-check-circle"></i> تم حفظ سجل التقدم بنجاح
        </div>
    @endif

    <!-- Card Header -->
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem;">
        <div>
            <div style="font-size:1rem;font-weight:700;color:#0C7261;">
                {{ $student?->full_name ?? '—' }}
            </div>
            <div style="font-size:0.78rem;color:#475569;margin-top:0.2rem;">
                <i class="fas fa-graduation-cap"></i>
                {{ $student?->gradeLevel?->name }}
            </div>
        </div>
        @if($existingId)
            <span class="badge badge-blue">
                <i class="fas fa-edit"></i> تحديث
            </span>
        @else
            <span class="badge badge-green">
                <i class="fas fa-plus"></i> إدخال جديد
            </span>
        @endif
    </div>

    <!-- Row 1: Date + Score -->
    <div class="grid-2" style="margin-bottom:1rem;">
        <div>
            <label class="form-label">
                <i class="fas fa-calendar-day" style="color:#0C7261;"></i> التاريخ
            </label>
            <input type="date" wire:model.live="date"
                   class="form-input" max="{{ date('Y-m-d') }}">
        </div>
        <div>
            <label class="form-label">
                <i class="fas fa-star" style="color:#f59e0b;"></i> الدرجة (0–10)
            </label>
            <input type="number" wire:model="score"
                   class="form-input" min="0" max="10" step="0.5"
                   placeholder="مثال: 8.5">
            @error('score')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- Row 2: Attendance + Interaction -->
    <div class="grid-2" style="margin-bottom:1rem;">
        <div>
            <label class="form-label">
                <i class="fas fa-user-check" style="color:#10b981;"></i> الحضور
            </label>
            <select wire:model="attendanceStatus" class="form-select">
                <option value="present">✅ حاضر</option>
                <option value="absent">❌ غائب</option>
                <option value="late">⏰ متأخر</option>
            </select>
        </div>
        <div>
            <label class="form-label">
                <i class="fas fa-lightbulb" style="color:#a78bfa;"></i> مستوى التفاعل
            </label>
            <select wire:model="interactionLevel" class="form-select">
                <option value="engaged">🟢 متفاعل</option>
                <option value="not_engaged">🔴 غير متفاعل</option>
            </select>
        </div>
    </div>

    {{-- <!-- Homework checkbox -->
    <div style="margin-bottom:1rem;display:flex;align-items:center;gap:0.75rem;
                padding:0.85rem 1rem;background:rgba(15,23,42,0.5);
                border-radius:0.75rem;border:1px solid rgba(12, 114, 97, 0.2);">
        <input type="checkbox" wire:model="homeworkSubmitted"
               id="hw-check-{{ $studentId }}"
               style="width:18px;height:18px;accent-color:#0C7261;cursor:pointer;">
        <label for="hw-check-{{ $studentId }}"
               style="color:#475569;font-size:0.9rem;cursor:pointer;user-select:none;">
            <i class="fas fa-book-open" style="color:#0C7261;margin-left:0.4rem;"></i>
            سُلِّم الواجب المنزلي
        </label>
    </div> --}}

    <!-- Comment -->
    <div style="margin-bottom:1.25rem;">
        <label class="form-label">
            <i class="fas fa-comment-alt" style="color:#475569;"></i>
            ملاحظات (اختياري)
        </label>
        {{-- <textarea wire:model="comment" class="form-input" rows="2"
                  placeholder="أضف ملاحظة عن الطالب..."></textarea> --}}
            <select wire:model="interactionLevel" class="form-select">
                <option value="engaged">🟢 متفاعل</option>
                <option value="not_engaged">🔴 غير متفاعل</option>
            </select>
        @error('comment')
            <span class="form-error">{{ $message }}</span>
        @enderror

    </div>

    <!-- Save button -->
    <button wire:click="save" wire:loading.attr="disabled"
            class="btn-primary" style="width:100%;justify-content:center;padding:0.75rem;">
        <span wire:loading.remove wire:target="save">
            <i class="fas fa-save"></i>
            {{ $existingId ? 'تحديث السجل' : 'حفظ التقدم' }}
        </span>
        <span wire:loading wire:target="save">
            <i class="fas fa-spinner fa-spin"></i> جارٍ الحفظ...
        </span>
    </button>
</div>
