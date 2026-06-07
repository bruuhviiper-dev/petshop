@php
    $statusClasses = [
        'pendente'     => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
        'confirmado'   => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
        'em_andamento' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-200',
        'concluido'    => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200',
        'cancelado'    => 'bg-gray-100 text-gray-500 line-through dark:bg-gray-700 dark:text-gray-400',
    ];
    $agPayload = [
        'id'             => $ag->id,
        'pet'            => $ag->pet?->name,
        'cliente'        => $ag->cliente?->name,
        'servico'        => $ag->servico?->name,
        'valor'          => 'R$ ' . number_format($ag->valor, 2, ',', '.'),
        'status'         => $ag->status,
        'colaborador_id' => $ag->colaborador_id,
        'hora'           => $ag->scheduled_at->format('H:i'),
        'data'           => $ag->scheduled_at->isoFormat('ddd, D [de] MMM'),
        'notes'          => $ag->notes,
    ];
@endphp
<div class="rounded p-1.5 text-xs cursor-pointer mb-0.5 {{ $statusClasses[$ag->status] ?? '' }}"
     @click.stop="editAgendamento(@js($agPayload))">
    <p class="font-medium truncate">{{ $ag->pet?->name }}</p>
    <p class="truncate opacity-75">{{ $ag->servico?->name }}</p>
</div>
