@props([
    'variant' => 'blue', // green, red, yellow, blue, gray
])

@php
$colorStyle = match($variant) {
    'green' => 'background:rgba(16,185,129,0.15);color:#059669;border:1px solid rgba(16,185,129,0.3);',
    'red' => 'background:rgba(239,68,68,0.15);color:#dc2626;border:1px solid rgba(239,68,68,0.3);',
    'yellow' => 'background:rgba(245,158,11,0.15);color:#d97706;border:1px solid rgba(245,158,11,0.3);',
    'blue' => 'background:rgba(12,114,97,0.15);color:#0C7261;border:1px solid rgba(12,114,97,0.3);',
    default => 'background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;',
};
@endphp

<span {{ $attributes->merge(['style' => 'display:inline-flex;align-items:center;gap:0.3rem;padding:0.2rem 0.6rem;border-radius:9999px;font-size:0.75rem;font-weight:600;' . $colorStyle]) }}>
    {{ $slot }}
</span>
