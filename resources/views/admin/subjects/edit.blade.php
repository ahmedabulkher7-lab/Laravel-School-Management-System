@extends('layouts.admin')
@section('title', 'تعديل مادة')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">تعديل المادة: {{ $subject->name_ar ?? $subject->name }}</h1>
    </div>
    <a href="{{ route('admin.subjects.index') }}" class="btn-secondary">
        <i class="fas fa-arrow-right"></i> عودة
    </a>
</div>

<div class="card" style="max-width:800px;">
    <div class="card-body">
        <form action="{{ route('admin.subjects.update', $subject) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">اسم المادة (عربي) <span style="color:#ef4444">*</span></label>
                    <input type="text" name="name_ar" class="form-input" value="{{ old('name_ar', $subject->name_ar) }}" required>
                    @error('name_ar') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">اسم المادة (إنجليزي)</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $subject->name) }}" required>
                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group" style="max-width:200px;">
                <label class="form-label">اللون <span style="color:#ef4444">*</span></label>
                <div style="display:flex;align-items:center;gap:1rem;">
                    <input type="color" name="color" value="{{ old('color', $subject->color) }}" style="width:40px;height:40px;border:none;background:none;cursor:pointer;padding:0;">
                </div>
                @error('color') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">المراحل الدراسية</label>
                <div style="background:#f8fafc;border:1px solid rgba(71,85,105,0.6);border-radius:0.75rem;padding:1rem;display:flex;flex-wrap:wrap;gap:1rem;">
                    @php $assignedLevels = old('grade_level_ids', $subject->gradeLevels->pluck('id')->toArray()); @endphp
                    @foreach($gradeLevels as $gl)
                    <label style="display:flex;align-items:center;gap:0.4rem;color:#475569;cursor:pointer;">
                        <input type="checkbox" name="grade_level_ids[]" value="{{ $gl->id }}"
                            {{ in_array($gl->id, $assignedLevels) ? 'checked' : '' }}
                            style="accent-color:#0C7261;width:16px;height:16px;">
                        {{ $gl->name }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div style="margin-top:1.5rem;text-align:left;">
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> تحديث المادة</button>
            </div>
        </form>
    </div>
</div>
@endsection
