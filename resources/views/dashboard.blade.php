<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    {{-- Saudação --}}
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Olá, {{ explode(' ', Auth::user()->name)[0] }} 👋</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ ucfirst(now()->translatedFormat('l, d \d\e F \d\e Y')) }}</p>
    </div>

    {{-- ═══ Cartões de métricas ═══ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- Agendamentos hoje (destaque com cor da marca) --}}
        <div class="relative overflow-hidden rounded-2xl p-5 text-white shadow-lg bg-brand">
            <div class="absolute -top-6 -right-6 w-24 h-24 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-8 -left-4 w-20 h-20 rounded-full bg-white/5"></div>
            <div class="relative flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-white/80">Agendamentos hoje</p>
                    <p class="text-3xl font-bold mt-1">{{ $agendamentosHoje->count() }}</p>
                </div>
                <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="relative mt-3 flex gap-2 flex-wrap">
                @foreach($porStatus as $status => $count)
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-white/20 backdrop-blur">{{ ucfirst(str_replace('_',' ',$status)) }}: {{ $count }}</span>
                @endforeach
            </div>
        </div>

        @php
        $metrics = [
            ['Ocupação hoje', $taxaOcupacao.'%', 'purple', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ['Faturamento mês', 'R$ '.number_format($faturamentoMes, 2, ',', '.'), 'emerald', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Retornos atrasados', $petsRetornoAtrasado->count(), 'amber', 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        ];
        $palette = [
            'purple'  => ['bg' => 'bg-purple-100 dark:bg-purple-900/30',   'tx' => 'text-purple-600 dark:text-purple-400'],
            'emerald' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'tx' => 'text-emerald-600 dark:text-emerald-400'],
            'amber'   => ['bg' => 'bg-amber-100 dark:bg-amber-900/30',     'tx' => 'text-amber-600 dark:text-amber-400'],
        ];
        @endphp
        @foreach($metrics as [$label, $value, $color, $icon])
        @php $p = $palette[$color]; @endphp
        <div class="rounded-2xl p-5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wide">{{ $label }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1 truncate">{{ $value }}</p>
                </div>
                <div class="w-11 h-11 {{ $p['bg'] }} rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 {{ $p['tx'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ═══ Gráfico + Top serviços ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2">
            <x-card title="Faturamento — últimos 30 dias">
                <canvas id="chartFaturamento" height="100"></canvas>
            </x-card>
        </div>
        <x-card title="Top serviços">
            @forelse($topServicos as $item)
                <div class="flex items-center justify-between py-2.5 border-b border-gray-50 dark:border-gray-700/50 last:border-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-7 h-7 rounded-lg bg-brand-soft text-brand text-xs font-bold flex items-center justify-center shrink-0">{{ $loop->iteration }}</span>
                        <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $item->servico?->name ?? 'N/A' }}</span>
                    </div>
                    <span class="text-sm font-semibold text-brand shrink-0">{{ $item->total }}x</span>
                </div>
            @empty
                <p class="text-sm text-gray-400 dark:text-gray-500 py-4 text-center">Nenhum serviço concluído ainda.</p>
            @endforelse
        </x-card>
    </div>

    {{-- ═══ Próximos agendamentos + Alertas ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card title="Próximos agendamentos">
            @forelse($proximosAgendamentos as $ag)
                <div class="flex items-center gap-3 py-2.5 border-b border-gray-50 dark:border-gray-700/50 last:border-0">
                    <div class="w-11 h-11 rounded-xl bg-brand-soft flex flex-col items-center justify-center shrink-0 leading-none">
                        <span class="text-xs font-bold text-brand">{{ $ag->scheduled_at->format('H:i') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $ag->pet?->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $ag->servico?->name }}</p>
                    </div>
                    <x-badge :status="$ag->status" />
                </div>
            @empty
                <p class="text-sm text-gray-400 dark:text-gray-500 py-4 text-center">Nenhum agendamento próximo.</p>
            @endforelse
            <a href="{{ route('agenda.index') }}" class="inline-flex items-center gap-1 mt-4 text-sm text-brand font-medium hover:gap-2 transition-all">Ver agenda completa →</a>
        </x-card>

        <x-card title="Alertas de retorno">
            @forelse($petsRetornoAtrasado as $pet)
                <div class="flex items-center gap-3 py-2.5 border-b border-gray-50 dark:border-gray-700/50 last:border-0">
                    <x-avatar :name="$pet->name" />
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $pet->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $pet->cliente?->name }}</p>
                    </div>
                    <a href="{{ route('clientes.show', $pet->cliente_id) }}" class="text-xs font-medium text-brand hover:underline shrink-0">Ver ficha</a>
                </div>
            @empty
                <p class="text-sm text-gray-400 dark:text-gray-500 py-4 text-center">Nenhum retorno atrasado. Tudo em dia! 🎉</p>
            @endforelse
        </x-card>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(255,255,255,0.06)' : '#f3f4f6';
        const tickColor = isDark ? '#9ca3af' : '#6b7280';
        const brandColor = getComputedStyle(document.documentElement).getPropertyValue('--color-brand').trim() || '#7C3AED';

        const el = document.getElementById('chartFaturamento');
        if (!el) return;

        const ctx = el.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 220);
        gradient.addColorStop(0, brandColor + '40');
        gradient.addColorStop(1, brandColor + '00');

        new Chart(el, {
            type: 'line',
            data: {
                labels: @json($faturamento30Dias->pluck('date')),
                datasets: [{ label: 'Faturamento', data: @json($faturamento30Dias->pluck('total')),
                    borderColor: brandColor, backgroundColor: gradient,
                    fill: true, tension: 0.4, pointRadius: 0, pointHoverRadius: 4, borderWidth: 2.5 }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => 'R$ ' + Number(c.raw).toLocaleString('pt-BR', {minimumFractionDigits: 2}) } } },
                scales: {
                    y: { ticks: { callback: v => 'R$ ' + v.toFixed(0), color: tickColor }, grid: { color: gridColor } },
                    x: { grid: { display: false }, ticks: { color: tickColor, maxTicksLimit: 8 } }
                }
            }
        });
    });
    </script>
</x-app-layout>
