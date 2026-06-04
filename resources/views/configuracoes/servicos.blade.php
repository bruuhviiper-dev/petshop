<x-app-layout>
    <x-slot name="title">Configurações — Serviços</x-slot>
    @include('configuracoes._nav')
    <div class="mt-4" x-data="{ editandoId: null }">
        <x-card title="Serviços">
            <div class="space-y-2 mb-4">
                @forelse($servicos as $s)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1 min-w-0" x-show="editandoId !== {{ $s->id }}">
                            <p class="text-sm font-medium text-gray-800">{{ $s->name }}</p>
                            <p class="text-xs text-gray-500">{{ $s->duration_minutes }}min · R$ {{ number_format($s->price, 2, ',', '.') }}</p>
                        </div>
                        <form x-show="editandoId === {{ $s->id }}" method="POST" action="{{ route('configuracoes.servicos.update', $s) }}" class="flex-1 flex items-center gap-2">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $s->name }}" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1">
                            <input type="number" name="duration_minutes" value="{{ $s->duration_minutes }}" class="w-16 text-sm border border-gray-300 rounded px-2 py-1">
                            <input type="number" step="0.01" name="price" value="{{ $s->price }}" class="w-20 text-sm border border-gray-300 rounded px-2 py-1">
                            <button type="submit" class="text-xs text-white bg-brand px-2 py-1 rounded">Salvar</button>
                        </form>
                        <div class="flex gap-2 shrink-0">
                            <button @click="editandoId = editandoId === {{ $s->id }} ? null : {{ $s->id }}" class="text-xs text-gray-500 hover:text-brand">Editar</button>
                            <x-confirm-delete :action="route('configuracoes.servicos.destroy', $s)" item-name="este serviço">
                                <button type="button" class="text-xs text-red-500 hover:text-red-700">Excluir</button>
                            </x-confirm-delete>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Nenhum serviço cadastrado.</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('configuracoes.servicos.store') }}" class="border-t border-gray-100 pt-4">
                @csrf
                <p class="text-xs font-medium text-gray-600 mb-2">Adicionar serviço</p>
                <div class="flex gap-2 flex-wrap">
                    <input type="text" name="name" placeholder="Nome do serviço" required class="flex-1 text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand min-w-40">
                    <input type="number" name="duration_minutes" placeholder="Duração (min)" required class="w-28 text-sm border border-gray-300 rounded-lg px-3 py-2">
                    <input type="number" step="0.01" name="price" placeholder="Preço (R$)" required class="w-28 text-sm border border-gray-300 rounded-lg px-3 py-2">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 rounded-lg">Adicionar</button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
