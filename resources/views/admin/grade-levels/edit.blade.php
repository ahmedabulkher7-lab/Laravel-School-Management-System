@extends('layouts.admin')
@section('title', 'تعديل مرحلة')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">تعديل المرحلة: {{ $gradeLevel->name }}</h1>
    </div>
    <a href="{{ route('admin.grade-levels.index') }}" class="btn-secondary">
        <i class="fas fa-arrow-right"></i> عودة
    </a>
</div>

<div class="card" style="max-width:500px;">
    <div class="card-body">
        <form action="{{ route('admin.grade-levels.update', $gradeLevel) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">اسم المرحلة <span style="color:#ef4444">*</span></label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $gradeLevel->name) }}" required>
                @error('name') <span class="form-error">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">الترتيب التسلسلي <span style="color:#ef4444">*</span></label>
                <input type="number" name="order" class="form-input" value="{{ old('order', $gradeLevel->order) }}" min="1" required>
                @error('order') <span class="form-error">{{ $message }}</span> @enderror
            </div>
            <div style="margin-top:1.5rem;text-align:left;">
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> تحديث</button>
            </div>
        </form>
    </div>
</div>
@endsection
