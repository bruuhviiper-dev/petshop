<x-app-layout>
    <x-slot name="title">Configurações — Colaboradores</x-slot>
    @include('configuracoes._nav')
    <div class="max-w-xl mt-4">
        <x-card title="Colaboradores">
            @forelse($colaboradores as $col)
                <div class="flex items-center gap-3 py-3 border-b border-gray-50 last:border-0">
                    <x-avatar :name="$col->user->name" />
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">{{ $col->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $col->cargo }} · {{ $col->comissao_percentual }}% comissão</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400">Nenhum colaborador cadastrado.</p>
            @endforelse
        </x-card>
    </div>
</x-app-layout>
