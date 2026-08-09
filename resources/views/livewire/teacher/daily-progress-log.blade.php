<div class="glass" style="padding:1.5rem;margin-bottom:1.25rem;transition:box-shadow 0.2s;">

    @if($saved)
        <div class="alert-success" style="margin-bottom:1rem;animation:fadeIn 0.3s ease;">
            <i class="fas fa-check-circle"></i> تم حفظ سجل التقدم بنجاح
        </div>
    @endif

    {{-- Card Header --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem;">
        <div>
            <div style="font-size:1rem;font-weight:700;color:#0C7261;">
                {{ $student?->full_name ?? '—' }}
            </div>
            <div style="font-size:0.78rem;color:#475569;margin-top:0.2rem;">
                <i class="fas fa-graduation-cap"></i>
                {{ $student?->gradeLevel?->name }}
            </div>
            @if($subjectName)
                <div style="margin-top:0.3rem;">
                    <span style="display:inline-flex;align-items:center;gap:0.3rem;
                                 background:rgba(12,114,97,0.1);color:#0C7261;
                                 font-size:0.72rem;font-weight:600;padding:0.2rem 0.6rem;
                                 border-radius:2rem;border:1px solid rgba(12,114,97,0.25);">
                        <i class="fas fa-book"></i> {{ $subjectName }}
                    </span>
                </div>
            @endif
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:0.4rem;">
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
    </div>

    {{-- Row 1: Date + Score --}}
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

    {{-- Row 2: Attendance + Interaction --}}
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

    {{-- Comment --}}
    <div style="margin-bottom:1.25rem;">
        <label class="form-label">
            <i class="fas fa-comment-alt" style="color:#475569;"></i>
            ملاحظات (اختياري)
        </label>
        <select wire:model.live="comment" class="form-select">
            <option value="">-- اختر ملاحظة --</option>
            <option value="متفوق جداً ويحقق نتائج متميزة">متفوق جداً ويحقق نتائج متميزة</option>
            <option value="يتفاعل مع المدرس ومتجاوب أثناء الحصة">يتفاعل مع المدرس ومتجاوب أثناء الحصة</option>
            <option value="مجتهد وملتزم بأداء الواجبات">مجتهد وملتزم بأداء الواجبات</option>
            <option value="يشارك بفاعلية في الأنشطة والمناقشات">يشارك بفاعلية في الأنشطة والمناقشات</option>
            <option value="يحتاج إلى مزيد من المتابعة والتشجيع">يحتاج إلى مزيد من المتابعة والتشجيع</option>
            <option value="يحتاج إلى تحسين التركيز أثناء الحصة">يحتاج إلى تحسين التركيز أثناء الحصة</option>
            <option value="تحسن ملحوظ في الأداء اليومي">تحسن ملحوظ في الأداء اليومي</option>
            @if($comment && !in_array($comment, [
                'متفوق جداً ويحقق نتائج متميزة',
                'يتفاعل مع المدرس ومتجاوب أثناء الحصة',
                'مجتهد وملتزم بأداء الواجبات',
                'يشارك بفاعلية في الأنشطة والمناقشات',
                'يحتاج إلى مزيد من المتابعة والتشجيع',
                'يحتاج إلى تحسين التركيز أثناء الحصة',
                'تحسن ملحوظ في الأداء اليومي',
            ], true))
                <option value="{{ $comment }}" selected>{{ $comment }}</option>
            @endif
        </select>
        @error('comment')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    {{-- Save button --}}
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
