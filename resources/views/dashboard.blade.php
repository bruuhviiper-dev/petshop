<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Agendamentos hoje</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $agendamentosHoje->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="mt-2 flex gap-2 flex-wrap">
                @foreach($porStatus as $status => $count)
                    <x-badge :status="$status" /> <span class="text-xs text-gray-500">{{ $count }}</span>
                @endforeach
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Ocupação hoje</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $taxaOcupacao }}%</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Faturamento mês</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">R$ {{ number_format($faturamentoMes, 2, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Retornos atrasados</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $petsRetornoAtrasado->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2">
            <x-card title="Faturamento — últimos 30 dias">
                <canvas id="chartFaturamento" height="100"></canvas>
            </x-card>
        </div>
        <x-card title="Top serviços">
            @forelse($topServicos as $item)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <span class="text-sm text-gray-700">{{ $item->servico?->name ?? 'N/A' }}</span>
                    <span class="text-sm font-semibold text-brand">{{ $item->total }}x</span>
                </div>
            @empty
                <p class="text-sm text-gray-400">Nenhum serviço concluído.</p>
            @endforelse
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card title="Próximos agendamentos">
            @forelse($proximosAgendamentos as $ag)
                <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                    <div class="w-10 h-10 rounded-lg bg-brand/10 flex items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-brand">{{ $ag->scheduled_at->format('H:i') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $ag->pet?->name }}</p>
                        <p class="text-xs text-gray-500">{{ $ag->servico?->name }}</p>
                    </div>
                    <x-badge :status="$ag->status" />
                </div>
            @empty
                <p class="text-sm text-gray-400">Nenhum agendamento.</p>
            @endforelse
            <a href="{{ route('agenda.index') }}" class="block mt-3 text-sm text-brand font-medium hover:underline">Ver agenda →</a>
        </x-card>

        <x-card title="Alertas de retorno">
            @forelse($petsRetornoAtrasado as $pet)
                <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                    <x-avatar :name="$pet->name" />
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800">{{ $pet->name }}</p>
                        <p class="text-xs text-gray-500">{{ $pet->cliente?->name }}</p>
                    </div>
                    <a href="{{ route('clientes.show', $pet->cliente_id) }}" class="text-xs text-brand hover:underline">Ver</a>
                </div>
            @empty
                <p class="text-sm text-gray-400">Nenhum retorno atrasado.</p>
            @endforelse
        </x-card>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const brandColor = getComputedStyle(document.documentElement).getPropertyValue('--color-brand').trim() || '#3B82F6';
        new Chart(document.getElementById('chartFaturamento'), {
            type: 'line',
            data: {
                labels: @json($faturamento30Dias->pluck('date')),
                datasets: [{ label: 'Faturamento', data: @json($faturamento30Dias->pluck('total')),
                    borderColor: brandColor, backgroundColor: brandColor + '20',
                    fill: true, tension: 0.4, pointRadius: 2 }]
            },
            options: {
                responsive: true, plugins: { legend: { display: false } },
                scales: {
                    y: { ticks: { callback: v => 'R$ ' + v.toFixed(0) }, grid: { color: '#f3f4f6' } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
    </script>
</x-app-layout>
