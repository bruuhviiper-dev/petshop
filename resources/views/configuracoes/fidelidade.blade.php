<x-app-layout>
    <x-slot name="title">Configurações — Fidelidade</x-slot>
    @include('configuracoes._nav')
    <div class="max-w-xl mt-4">
        <x-card title="Programa de Fidelidade">
            <p class="text-sm text-gray-500 mb-4">Configure quantos atendimentos o cliente precisa acumular para ganhar um desconto.</p>
            <form method="POST" action="{{ route('configuracoes.fidelidade.update') }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Atendimentos para prêmio</label>
                    <input type="number" name="atendimentos_para_premio" value="{{ old('atendimentos_para_premio', $config->atendimentos_para_premio ?? 10) }}" min="1" required class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Desconto (%) no prêmio</label>
                    <input type="number" step="0.01" name="desconto_percentual" value="{{ old('desconto_percentual', $config->desconto_percentual ?? 10) }}" min="0" max="100" required class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                </div>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 rounded-lg">Salvar</button>
            </form>
        </x-card>
    </div>
</x-app-layout>
