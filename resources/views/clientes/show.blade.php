<x-app-layout>
    <x-slot name="title">{{ $cliente->name }}</x-slot>

    <div class="mb-4">
        <a href="{{ route('clientes.index') }}" class="text-sm text-gray-500 hover:text-brand">← Clientes</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Dados do cliente --}}
        <div class="space-y-4">
            <x-card>
                <div class="text-center mb-4">
                    <x-avatar :name="$cliente->name" class="w-16 h-16 text-xl mx-auto mb-2" />
                    <h2 class="text-lg font-bold text-gray-800">{{ $cliente->name }}</h2>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $cliente->phone }}
                    </div>
                    @if($cliente->email)
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $cliente->email }}
                    </div>
                    @endif
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 flex gap-2">
                    <a href="{{ route('clientes.edit', $cliente) }}" class="flex-1 text-center py-2 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">Editar</a>
                    <x-confirm-delete :action="route('clientes.destroy', $cliente)" item-name="este cliente">
                        <button type="button" class="flex-1 py-2 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg">Excluir</button>
                    </x-confirm-delete>
                </div>
            </x-card>

            {{-- Pets --}}
            <x-card title="Pets">
                @forelse($cliente->pets as $pet)
                    <a href="{{ route('clientes.pets.show', [$cliente, $pet]) }}"
                       class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0 hover:bg-gray-50 -mx-1 px-1 rounded transition-colors">
                        @if($pet->photo)
                            <img src="{{ Storage::url($pet->photo) }}" class="w-10 h-10 rounded-full object-cover shrink-0">
                        @else
                            <x-avatar :name="$pet->name" />
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800">{{ $pet->name }}</p>
                            <p class="text-xs text-gray-500">{{ $pet->species }} @if($pet->breed)— {{ $pet->breed }}@endif</p>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-gray-400">Nenhum pet cadastrado.</p>
                @endforelse
                <a href="{{ route('clientes.pets.create', $cliente) }}"
                   class="block mt-3 text-center py-2 text-xs font-medium text-brand border border-brand/30 hover:bg-brand/5 rounded-lg">
                    + Adicionar pet
                </a>
            </x-card>
        </div>

        {{-- Histórico de atendimentos --}}
        <div class="lg:col-span-2">
            <x-card title="Histórico de atendimentos">
                @forelse($cliente->agendamentos as $ag)
                    <div class="flex items-start gap-3 py-3 border-b border-gray-50 last:border-0">
                        <div class="w-12 text-center shrink-0">
                            <p class="text-xs font-bold text-gray-800">{{ $ag->scheduled_at->format('d/m') }}</p>
                            <p class="text-xs text-gray-400">{{ $ag->scheduled_at->format('Y') }}</p>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-medium text-gray-800">{{ $ag->servico?->name }}</p>
                                <x-badge :status="$ag->status" />
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Pet: {{ $ag->pet?->name }}
                                @if($ag->colaborador) · {{ $ag->colaborador->user->name }} @endif
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-semibold text-gray-800">R$ {{ number_format($ag->valor, 2, ',', '.') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Nenhum atendimento registrado.</p>
                @endforelse
            </x-card>
        </div>
    </div>
</x-app-layout>
