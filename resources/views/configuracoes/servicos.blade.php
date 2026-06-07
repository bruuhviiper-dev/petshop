<x-app-layout>
    <x-slot name="title">Configurações — Serviços</x-slot>
    @include('configuracoes._nav')
    <div class="mt-4 max-w-2xl" x-data="{ editandoId: null }">
        <x-card title="Serviços">
            <div class="space-y-2 mb-4">
                @forelse($servicos as $s)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/40 rounded-lg">
                        <div class="flex-1 min-w-0" x-show="editandoId !== {{ $s->id }}">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                {{ $s->name }}
                                @unless($s->active)<span class="text-xs text-gray-400 dark:text-gray-500">(inativo)</span>@endunless
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $s->duration_minutes }}min · R$ {{ number_format($s->price, 2, ',', '.') }}</p>
                        </div>
                        <form x-show="editandoId === {{ $s->id }}" x-cloak method="POST" action="{{ route('configuracoes.servicos.update', $s) }}" class="flex-1 flex items-center gap-2 flex-wrap">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $s->name }}" class="form-input flex-1 min-w-32 !py-1">
                            <input type="number" name="duration_minutes" value="{{ $s->duration_minutes }}" class="form-input w-16 !py-1">
                            <input type="number" step="0.01" name="price" value="{{ $s->price }}" class="form-input w-20 !py-1">
                            <label class="inline-flex items-center gap-1 text-xs text-gray-600 dark:text-gray-300">
                                <input type="checkbox" name="active" value="1" @checked($s->active) class="rounded border-gray-300 dark:border-gray-600 text-brand focus:ring-brand/20">
                                Ativo
                            </label>
                            <button type="submit" class="text-xs text-white bg-brand px-2 py-1 rounded">Salvar</button>
                        </form>
                        <div class="flex gap-2 shrink-0">
                            <button @click="editandoId = editandoId === {{ $s->id }} ? null : {{ $s->id }}" class="text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-brand">Editar</button>
                            <x-confirm-delete :action="route('configuracoes.servicos.destroy', $s)" item-name="este serviço">
                                <button type="button" class="text-xs font-medium text-red-500 hover:text-red-700">Excluir</button>
                            </x-confirm-delete>
                        </div>
                    </div>
                @empty
                    <x-empty-state icon="badge" title="Nenhum serviço" description="Cadastre os serviços oferecidos (banho, tosa, consulta…) para usá-los na agenda." />
                @endforelse
            </div>

            <form method="POST" action="{{ route('configuracoes.servicos.store') }}" class="border-t border-gray-100 dark:border-gray-700 pt-4">
                @csrf
                <p class="form-label">Adicionar serviço</p>
                <div class="flex gap-2 flex-wrap">
                    <input type="text" name="name" placeholder="Nome do serviço" required class="form-input flex-1 min-w-40">
                    <input type="number" name="duration_minutes" placeholder="Duração (min)" required class="form-input w-32">
                    <input type="number" step="0.01" name="price" placeholder="Preço (R$)" required class="form-input w-32">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 rounded-lg">Adicionar</button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
