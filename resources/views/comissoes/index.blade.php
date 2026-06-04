<x-app-layout>
    <x-slot name="title">Comissões</x-slot>

    <div class="space-y-4">
        @forelse($colaboradores as $col)
            <x-card :title="$col->user->name . ' — ' . $col->cargo">
                <div class="flex items-center gap-6 mb-4 text-sm">
                    <div>
                        <span class="text-gray-500">Pendente:</span>
                        <span class="font-bold text-amber-600 ml-1">R$ {{ number_format($col->total_pendente, 2, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Pago:</span>
                        <span class="font-bold text-green-600 ml-1">R$ {{ number_format($col->total_pago, 2, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Comissão:</span>
                        <span class="font-medium ml-1">{{ $col->comissao_percentual }}%</span>
                    </div>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 border-b border-gray-100">
                            <th class="pb-2 pr-4">Serviço</th>
                            <th class="pb-2 pr-4">Data</th>
                            <th class="pb-2 pr-4">Valor</th>
                            <th class="pb-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($col->comissoes as $com)
                            <tr>
                                <td class="py-2 pr-4 text-gray-700">{{ $com->agendamento?->servico?->name ?? 'N/A' }}</td>
                                <td class="py-2 pr-4 text-gray-500">{{ $com->agendamento?->scheduled_at?->format('d/m/Y') }}</td>
                                <td class="py-2 pr-4 font-semibold text-gray-800">R$ {{ number_format($com->amount, 2, ',', '.') }}</td>
                                <td class="py-2">
                                    @if($com->paid)
                                        <span class="text-xs text-green-600 font-medium">Pago em {{ $com->paid_at?->format('d/m') }}</span>
                                    @else
                                        <form method="POST" action="{{ route('comissoes.pagar', $com) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-xs text-amber-700 bg-amber-100 hover:bg-amber-200 px-2 py-1 rounded font-medium">Marcar pago</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-card>
        @empty
            <x-card><p class="text-sm text-gray-400">Nenhum colaborador cadastrado.</p></x-card>
        @endforelse
    </div>
</x-app-layout>
