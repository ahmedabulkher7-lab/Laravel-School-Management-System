@props([
    'name',
    'label' => null,
    'required' => false,
    'placeholder' => 'اختر من القائمة...',
])

<div class="form-group" style="margin-bottom: 1rem;">
    @if($label)
        <label for="{{ $name }}" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;font-size:0.85rem;color:#334155;">
            {{ $label }}
            @if($required) <span style="color:#ef4444;">*</span> @endif
        </label>
    @endif

    <select 
        name="{{ $name }}" 
        id="{{ $name }}" 
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'form-select' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        style="width:100%;padding:0.55rem 0.85rem;border:1px solid #cbd5e1;border-radius:6px;font-size:0.875rem;"
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>

    @error($name)
        <div style="color:#ef4444;font-size:0.75rem;margin-top:0.3rem;">
            {{ $message }}
        </div>
    @enderror
</div>
