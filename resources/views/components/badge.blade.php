@props(['status' => 'pendente'])

@php
$colors = [
    'pendente'     => 'bg-amber-100 text-amber-800',
    'confirmado'   => 'bg-blue-100 text-blue-800',
    'em_andamento' => 'bg-purple-100 text-purple-800',
    'concluido'    => 'bg-green-100 text-green-800',
    'cancelado'    => 'bg-gray-100 text-gray-600',
];
$labels = [
    'pendente'     => 'Pendente',
    'confirmado'   => 'Confirmado',
    'em_andamento' => 'Em Andamento',
    'concluido'    => 'Concluído',
    'cancelado'    => 'Cancelado',
];
$class = $colors[$status] ?? 'bg-gray-100 text-gray-600';
$label = $labels[$status] ?? ucfirst($status);
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$class}"]) }}>
    {{ $label }}
</span>
