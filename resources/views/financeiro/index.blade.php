<x-app-layout>
    <x-slot name="title">Financeiro</x-slot>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-card>
            <p class="text-xs text-gray-500 uppercase tracking-wide">Receita</p>
            <p class="text-xl font-bold text-green-600 mt-1">R$ {{ number_format($receita, 2, ',', '.') }}</p>
        </x-card>
        <x-card>
            <p class="text-xs text-gray-500 uppercase tracking-wide">Despesas</p>
            <p class="text-xl font-bold text-red-500 mt-1">R$ {{ number_format($despesa, 2, ',', '.') }}</p>
        </x-card>
        <x-card>
            <p class="text-xs text-gray-500 uppercase tracking-wide">Lucro líquido</p>
            <p class="text-xl font-bold {{ $lucro >= 0 ? 'text-green-600' : 'text-red-500' }} mt-1">R$ {{ number_format($lucro, 2, ',', '.') }}</p>
        </x-card>
        <x-card>
            <p class="text-xs text-gray-500 uppercase tracking-wide">Ticket médio</p>
            <p class="text-xl font-bold text-gray-800 mt-1">R$ {{ number_format($ticketMedio, 2, ',', '.') }}</p>
        </x-card>
    </div>

    <x-card>
        <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
            <form method="GET" action="{{ route('financeiro.index') }}" class="flex gap-2 flex-wrap">
                <input type="date" name="inicio" value="{{ request('inicio') }}" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                <input type="date" name="fim" value="{{ request('fim') }}" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                <select name="type" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                    <option value="">Todos</option>
                    <option value="receita" {{ request('type') === 'receita' ? 'selected' : '' }}>Receitas</option>
                    <option value="despesa" {{ request('type') === 'despesa' ? 'selected' : '' }}>Despesas</option>
                </select>
                <button type="submit" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-sm rounded-lg text-gray-700">Filtrar</button>
            </form>
            <a href="{{ route('financeiro.exportar', request()->all()) }}"
               class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">Exportar CSV</a>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 uppercase tracking-wide border-b border-gray-100">
                    <th class="pb-3 pr-4">Tipo</th>
                    <th class="pb-3 pr-4">Descrição</th>
                    <th class="pb-3 pr-4">Valor</th>
                    <th class="pb-3">Pago em</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($registros as $r)
                    <tr>
                        <td class="py-3 pr-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $r->type === 'receita' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $r->type === 'receita' ? 'Receita' : 'Despesa' }}
                            </span>
                        </td>
                        <td class="py-3 pr-4 text-gray-700">{{ $r->description }}</td>
                        <td class="py-3 pr-4 font-semibold {{ $r->type === 'receita' ? 'text-green-600' : 'text-red-500' }}">
                            R$ {{ number_format($r->amount, 2, ',', '.') }}
                        </td>
                        <td class="py-3 text-gray-500 text-xs">{{ $r->paid_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-8 text-center text-sm text-gray-400">Nenhum registro encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($registros->hasPages())
            <div class="mt-4 pt-4 border-t border-gray-100">{{ $registros->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
