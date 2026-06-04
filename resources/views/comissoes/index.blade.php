<x-app-layout>
    <x-slot name="title">Comissões</x-slot>

    <div x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 500)" class="space-y-4">

        {{-- Skeleton loader --}}
        <div x-show="loading" x-cloak class="space-y-4">
            <x-card>
                <x-skeleton-table :rows="4" :cols="4" />
            </x-card>
        </div>

        {{-- Conteúdo real --}}
        <div x-show="!loading">
            @forelse($colaboradores as $col)
                <x-card :title="$col->user->name . ' — ' . $col->cargo">
                    <div class="flex items-center gap-6 mb-4 text-sm flex-wrap">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Pendente:</span>
                            <span class="font-bold text-amber-600 ml-1">R$ {{ number_format($col->total_pendente, 2, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Pago:</span>
                            <span class="font-bold text-green-600 ml-1">R$ {{ number_format($col->total_pago, 2, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Comissão:</span>
                            <span class="font-medium ml-1 dark:text-gray-200">{{ $col->comissao_percentual }}%</span>
                        </div>
                    </div>
                    @if($col->comissoes->isEmpty())
                        <x-empty-state icon="badge" title="Nenhuma comissão encontrada" description="Este colaborador ainda não tem comissões registradas." />
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm" aria-label="Comissões de {{ $col->user->name }}">
                                <thead>
                                    <tr class="text-left text-xs text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                                        <th class="pb-2 pr-4">Serviço</th>
                                        <th class="pb-2 pr-4">Data</th>
                                        <th class="pb-2 pr-4">Valor</th>
                                        <th class="pb-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                    @foreach($col->comissoes as $com)
                                        <tr>
                                            <td class="py-2 pr-4 text-gray-700 dark:text-gray-300">{{ $com->agendamento?->servico?->name ?? 'N/A' }}</td>
                                            <td class="py-2 pr-4 text-gray-500 dark:text-gray-400">{{ $com->agendamento?->scheduled_at?->format('d/m/Y') }}</td>
                                            <td class="py-2 pr-4 font-semibold text-gray-800 dark:text-gray-100">R$ {{ number_format($com->amount, 2, ',', '.') }}</td>
                                            <td class="py-2">
                                                @if($com->paid)
                                                    <span class="text-xs text-green-600 font-medium">Pago em {{ $com->paid_at?->format('d/m') }}</span>
                                                @else
                                                    <form method="POST" action="{{ route('comissoes.pagar', $com) }}" class="inline">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="text-xs text-amber-700 bg-amber-100 hover:bg-amber-200 px-2 py-1 rounded font-medium" aria-label="Marcar comissão como paga">Marcar pago</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </x-card>
            @empty
                <x-card>
                    <x-empty-state icon="badge" title="Nenhuma comissão encontrada" description="Não há colaboradores cadastrados para exibir comissões." />
                </x-card>
            @endforelse
        </div>
    </div>
</x-app-layout>
