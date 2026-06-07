<x-app-layout>
    <x-slot name="title">Histórico de Vendas</x-slot>

    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('pdv.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-brand">← Voltar ao PDV</a>
    </div>

    <x-card class="!p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Data</th>
                        <th class="px-4 py-3">Cliente</th>
                        <th class="px-4 py-3">Itens</th>
                        <th class="px-4 py-3">Pagamento</th>
                        <th class="px-4 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/60">
                    @forelse($vendas as $v)
                        <tr class="text-gray-700 dark:text-gray-200">
                            <td class="px-4 py-3 font-mono text-xs">{{ $v->id }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $v->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $v->cliente?->name ?? 'Consumidor final' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $v->itens->sum('quantity') }} item(ns)</td>
                            <td class="px-4 py-3 capitalize">{{ $v->payment_method }}</td>
                            <td class="px-4 py-3 text-right font-medium">R$ {{ number_format($v->total, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty-state icon="currency" title="Nenhuma venda" description="As vendas do PDV aparecerão aqui." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($vendas->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $vendas->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
