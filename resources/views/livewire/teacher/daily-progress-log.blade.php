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
                   class="form-input" max="{{ date('Y-m-d') }}" @disabled($isScheduledLesson)>
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

    <option value="طالب ممتاز ومتفاعل جداً أثناء الحصة، استمر بنفس المستوى 👏">
        1. طالب ممتاز ومتفاعل جداً أثناء الحصة، استمر بنفس المستوى 👏
    </option>

    <option value="ملتزم بالمواعيد ويشارك بشكل رائع، أداء مميز جداً.">
        2. ملتزم بالمواعيد ويشارك بشكل رائع، أداء مميز جداً.
    </option>

    <option value="تطور ملحوظ في المستوى، واضح إنه يبذل مجهود كبير.">
        3. تطور ملحوظ في المستوى، واضح إنه يبذل مجهود كبير.
    </option>

    <option value="متفاعل ومهتم بالدرس، ويجاوب بثقة واهتمام.">
        4. متفاعل ومهتم بالدرس، ويجاوب بثقة واهتمام.
    </option>

    <option value="طالب مجتهد وملتزم، ومستواه يتحسن بشكل مستمر.">
        5. طالب مجتهد وملتزم، ومستواه يتحسن بشكل مستمر.
    </option>

    <option value="أداء رائع اليوم، ومشاركة فعالة جداً أثناء الحصة.">
        6. أداء رائع اليوم، ومشاركة فعالة جداً أثناء الحصة.
    </option>

    <option value="ملتزم بالقواعد ويتعامل باحترام وتعاون، ممتاز جداً.">
        7. ملتزم بالقواعد ويتعامل باحترام وتعاون، ممتاز جداً.
    </option>

    <option value="عنده حماس واضح للتعلم ويحاول يطور من نفسه باستمرار.">
        8. عنده حماس واضح للتعلم ويحاول يطور من نفسه باستمرار.
    </option>

    <option value="مستواه جيد جداً، ومشاركته الإيجابية تساعد على نجاح الحصة.">
        9. مستواه جيد جداً، ومشاركته الإيجابية تساعد على نجاح الحصة.
    </option>

    <option value="شغل ممتاز وتقدم واضح، أتمنى له الاستمرار بنفس الحماس 🌟">
        10. شغل ممتاز وتقدم واضح، أتمنى له الاستمرار بنفس الحماس 🌟
    </option>

    <option value="محتاج يركز شوية أكتر أثناء الحصة، وإن شاء الله يتحسن مع الوقت.">
        11. محتاج يركز شوية أكتر أثناء الحصة، وإن شاء الله يتحسن مع الوقت.
    </option>

    <option value="المشاركة كانت قليلة اليوم، ونشجعه يحاول يشارك بشكل أكبر.">
        12. المشاركة كانت قليلة اليوم، ونشجعه يحاول يشارك بشكل أكبر.
    </option>

    <option value="محتاج اهتمام أكبر بالمواعيد والالتزام بوقت الحصة.">
        13. محتاج اهتمام أكبر بالمواعيد والالتزام بوقت الحصة.
    </option>

    <option value="يفضل مراجعة الدرس السابق بشكل أكبر للاستفادة من الحصة الجديدة.">
        14. يفضل مراجعة الدرس السابق بشكل أكبر للاستفادة من الحصة الجديدة.
    </option>

    <option value="محتاج يكون أكثر تفاعلاً أثناء الشرح، ومع التشجيع أكيد هيكون أفضل.">
        15. محتاج يكون أكثر تفاعلاً أثناء الشرح، ومع التشجيع أكيد هيكون أفضل.
    </option>

    <option value="فيه بعض التشتت أثناء الحصة، ونحتاج نركز أكبر المرة القادمة.">
        16. فيه بعض التشتت أثناء الحصة، ونحتاج نركز أكبر المرة القادمة.
    </option>

    <option value="محتاج يبذل مجهود إضافي بسيط في المتابعة والمراجعة.">
        17. محتاج يبذل مجهود إضافي بسيط في المتابعة والمراجعة.
    </option>

    <option value="الالتزام جيد، لكن نحتاج تحسين بسيط في المشاركة والتفاعل.">
        18. الالتزام جيد، لكن نحتاج تحسين بسيط في المشاركة والتفاعل.
    </option>

    <option value="يفضل الاهتمام بالواجب والمراجعة بشكل منتظم لتحسين المستوى.">
        19. يفضل الاهتمام بالواجب والمراجعة بشكل منتظم لتحسين المستوى.
    </option>

    <option value="الأداء مقبول، ومع تركيز ومشاركة أكثر هيكون التقدم أفضل بإذن الله.">
        20. الأداء مقبول، ومع تركيز ومشاركة أكثر هيكون التقدم أفضل بإذن الله.
    </option>

    @if($comment && !in_array($comment, [
        'طالب ممتاز ومتفاعل جداً أثناء الحصة، استمر بنفس المستوى 👏',
        'ملتزم بالمواعيد ويشارك بشكل رائع، أداء مميز جداً.',
        'تطور ملحوظ في المستوى، واضح إنه يبذل مجهود كبير.',
        'متفاعل ومهتم بالدرس، ويجاوب بثقة واهتمام.',
        'طالب مجتهد وملتزم، ومستواه يتحسن بشكل مستمر.',
        'أداء رائع اليوم، ومشاركة فعالة جداً أثناء الحصة.',
        'ملتزم بالقواعد ويتعامل باحترام وتعاون، ممتاز جداً.',
        'عنده حماس واضح للتعلم ويحاول يطور من نفسه باستمرار.',
        'مستواه جيد جداً، ومشاركته الإيجابية تساعد على نجاح الحصة.',
        'شغل ممتاز وتقدم واضح، أتمنى له الاستمرار بنفس الحماس 🌟',
        'محتاج يركز شوية أكتر أثناء الحصة، وإن شاء الله يتحسن مع الوقت.',
        'المشاركة كانت قليلة اليوم، ونشجعه يحاول يشارك بشكل أكبر.',
        'محتاج اهتمام أكبر بالمواعيد والالتزام بوقت الحصة.',
        'يفضل مراجعة الدرس السابق بشكل أكبر للاستفادة من الحصة الجديدة.',
        'محتاج يكون أكثر تفاعلاً أثناء الشرح، ومع التشجيع أكيد هيكون أفضل.',
        'فيه بعض التشتت أثناء الحصة، ونحتاج نركز أكبر المرة القادمة.',
        'محتاج يبذل مجهود إضافي بسيط في المتابعة والمراجعة.',
        'الالتزام جيد، لكن نحتاج تحسين بسيط في المشاركة والتفاعل.',
        'يفضل الاهتمام بالواجب والمراجعة بشكل منتظم لتحسين المستوى.',
        'الأداء مقبول، ومع تركيز ومشاركة أكثر هيكون التقدم أفضل بإذن الله.',
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
