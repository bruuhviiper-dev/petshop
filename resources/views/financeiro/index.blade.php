<x-app-layout>
    <x-slot name="title">Financeiro</x-slot>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-card>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Receita</p>
            <p class="text-xl font-bold text-green-600 mt-1">R$ {{ number_format($receita, 2, ',', '.') }}</p>
        </x-card>
        <x-card>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Despesas</p>
            <p class="text-xl font-bold text-red-500 mt-1">R$ {{ number_format($despesa, 2, ',', '.') }}</p>
        </x-card>
        <x-card>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Lucro líquido</p>
            <p class="text-xl font-bold {{ $lucro >= 0 ? 'text-green-600' : 'text-red-500' }} mt-1">R$ {{ number_format($lucro, 2, ',', '.') }}</p>
        </x-card>
        <x-card>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ticket médio</p>
            <p class="text-xl font-bold text-gray-800 dark:text-gray-100 mt-1">R$ {{ number_format($ticketMedio, 2, ',', '.') }}</p>
        </x-card>
    </div>

    <div x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 500)">
        <x-card>
            <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                <form method="GET" action="{{ route('financeiro.index') }}" class="flex gap-2 flex-wrap">
                    <label for="data-inicio" class="sr-only">Data início</label>
                    <input id="data-inicio" type="date" name="inicio" value="{{ request('inicio') }}" class="text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                    <label for="data-fim" class="sr-only">Data fim</label>
                    <input id="data-fim" type="date" name="fim" value="{{ request('fim') }}" class="text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                    <label for="tipo-filtro" class="sr-only">Tipo</label>
                    <select id="tipo-filtro" name="type" class="text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                        <option value="">Todos</option>
                        <option value="receita" {{ request('type') === 'receita' ? 'selected' : '' }}>Receitas</option>
                        <option value="despesa" {{ request('type') === 'despesa' ? 'selected' : '' }}>Despesas</option>
                    </select>
                    <button type="submit" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 text-sm rounded-lg text-gray-700">Filtrar</button>
                </form>
                <a href="{{ route('financeiro.exportar', request()->all()) }}"
                   class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700" aria-label="Exportar dados como CSV">Exportar CSV</a>
            </div>

            {{-- Skeleton loader --}}
            <div x-show="loading" x-cloak>
                <x-skeleton-table :rows="5" :cols="4" />
            </div>

            {{-- Tabela real --}}
            <div x-show="!loading" class="overflow-x-auto">
                <table class="w-full text-sm" aria-label="Registros financeiros">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-100 dark:border-gray-700">
                            <th class="pb-3 pr-4">Tipo</th>
                            <th class="pb-3 pr-4">Descrição</th>
                            <th class="pb-3 pr-4">Valor</th>
                            <th class="pb-3">Pago em</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @forelse($registros as $r)
                            <tr class="dark:text-gray-100">
                                <td class="py-3 pr-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $r->type === 'receita' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $r->type === 'receita' ? 'Receita' : 'Despesa' }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 text-gray-700 dark:text-gray-300">{{ $r->description }}</td>
                                <td class="py-3 pr-4 font-semibold {{ $r->type === 'receita' ? 'text-green-600' : 'text-red-500' }}">
                                    R$ {{ number_format($r->amount, 2, ',', '.') }}
                                </td>
                                <td class="py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $r->paid_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-2">
                                    <x-empty-state
                                        icon="currency"
                                        title="Sem registros no período"
                                        description="Não há lançamentos financeiros para o período selecionado." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($registros->hasPages())
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">{{ $registros->links() }}</div>
            @endif
        </x-card>
    </div>
</x-app-layout>
