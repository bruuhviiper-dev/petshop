@props(['name' => 'U', 'class' => ''])

@php
$initials = collect(explode(' ', $name))
    ->filter()
    ->take(2)
    ->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))
    ->implode('');
@endphp

<div {{ $attributes->merge(['class' => "w-8 h-8 rounded-full bg-brand flex items-center justify-center text-white font-semibold text-xs shrink-0 {$class}"]) }}>
    {{ $initials }}
</div>
