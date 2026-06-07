<x-app-layout>
    <x-slot name="title">{{ $pet->name }}</x-slot>
    <div class="mb-4"><a href="{{ route('clientes.show', $cliente) }}" class="text-sm text-gray-500 hover:text-brand">← {{ $cliente->name }}</a></div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="space-y-4">
            <x-card>
                @if($pet->photo)
                    <img src="{{ Storage::url($pet->photo) }}" class="w-full h-40 object-cover rounded-lg mb-3">
                @else
                    <div class="w-full h-32 bg-gray-100 rounded-lg flex items-center justify-center mb-3">
                        <span class="text-4xl">🐾</span>
                    </div>
                @endif
                <h2 class="text-lg font-bold text-gray-800 text-center">{{ $pet->name }}</h2>
                <p class="text-sm text-gray-500 text-center">{{ $pet->species }} @if($pet->breed)— {{ $pet->breed }}@endif</p>
                <div class="mt-4 space-y-2 text-sm">
                    @if($pet->weight) <div class="flex justify-between"><span class="text-gray-500">Peso</span><span class="font-medium">{{ $pet->weight }} kg</span></div> @endif
                    @if($pet->temperament) <div class="flex justify-between"><span class="text-gray-500">Temperamento</span><span class="font-medium">{{ $pet->temperament }}</span></div> @endif
                    <div class="flex justify-between"><span class="text-gray-500">Retorno a cada</span><span class="font-medium">{{ $pet->retorno_dias }} dias</span></div>
                </div>
                @if($pet->allergies)
                    <div class="mt-3 p-2 bg-red-50 rounded-lg text-xs text-red-700">⚠️ Alergias: {{ $pet->allergies }}</div>
                @endif
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('clientes.pets.edit', [$cliente, $pet]) }}" class="flex-1 text-center py-2 text-xs font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">Editar</a>
                </div>
            </x-card>

            {{-- Carteirinha digital com QR Code --}}
            @php $carteirinhaUrl = route('pet.carteirinha', $pet->public_token); @endphp
            <x-card title="Carteirinha digital (QR)">
                <div x-data="{ copiado: false, copiar() { navigator.clipboard.writeText('{{ $carteirinhaUrl }}'); this.copiado = true; setTimeout(() => this.copiado = false, 2000); } }"
                     class="flex items-center gap-4">
                    <a href="{{ $carteirinhaUrl }}" target="_blank" class="shrink-0 [&>svg]:w-24 [&>svg]:h-24 rounded-lg overflow-hidden bg-white p-1">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(96)->margin(0)->generate($carteirinhaUrl) !!}
                    </a>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Ficha pública (vacinas, alergias, contatos). O tutor salva no celular; a recepção escaneia no check-in.</p>
                        <div class="flex gap-2 flex-wrap">
                            <a href="{{ $carteirinhaUrl }}" target="_blank" class="px-3 py-1.5 text-xs font-medium text-white bg-brand rounded-lg hover:opacity-90">Abrir</a>
                            <button type="button" @click="copiar()" class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">
                                <span x-show="!copiado">Copiar link</span><span x-show="copiado" x-cloak class="text-green-600 dark:text-green-400">Copiado!</span>
                            </button>
                        </div>
                    </div>
                </div>
            </x-card>
            <x-card title="Vacinas">
                @forelse($pet->vacinas as $v)
                    <div class="py-2 border-b border-gray-50 dark:border-gray-700 last:border-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $v->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Aplicada: {{ $v->date_applied->format('d/m/Y') }}</p>
                        @if($v->next_date) <p class="text-xs {{ $v->next_date->isPast() ? 'text-red-500' : 'text-green-600' }}">Próxima: {{ $v->next_date->format('d/m/Y') }}</p> @endif
                    </div>
                @empty
                    <x-empty-state icon="syringe" title="Nenhuma vacina registrada" description="Adicione o histórico de vacinação deste pet." />
                @endforelse
            </x-card>
        </div>
        <div class="lg:col-span-2">
            <x-card title="Histórico de atendimentos">
                @forelse($pet->agendamentos as $ag)
                    <div class="flex items-start gap-3 py-3 border-b border-gray-50 last:border-0">
                        <div class="w-12 text-center shrink-0">
                            <p class="text-xs font-bold text-gray-800">{{ $ag->scheduled_at->format('d/m') }}</p>
                            <p class="text-xs text-gray-400">{{ $ag->scheduled_at->format('Y') }}</p>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-medium text-gray-800">{{ $ag->servico?->name }}</p>
                                <x-badge :status="$ag->status" />
                            </div>
                            @if($ag->colaborador) <p class="text-xs text-gray-500">{{ $ag->colaborador->user->name }}</p> @endif
                        </div>
                        <p class="text-sm font-semibold text-gray-800 shrink-0">R$ {{ number_format($ag->valor, 2, ',', '.') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Nenhum atendimento.</p>
                @endforelse
            </x-card>
        </div>
    </div>
</x-app-layout>
