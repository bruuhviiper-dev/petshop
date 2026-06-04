<x-app-layout>
    <x-slot name="title">Configurações — Horários</x-slot>
    @include('configuracoes._nav')
    <div class="max-w-xl mt-4">
        <x-card title="Horários de Funcionamento">
            <form method="POST" action="{{ route('configuracoes.horarios.update') }}" class="space-y-2">
                @csrf @method('PUT')
                @php $dias = ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado']; @endphp
                @foreach($dias as $i => $dia)
                    @php $h = $horarios->get($i); @endphp
                    <div class="flex items-center gap-4 py-2 border-b border-gray-50 last:border-0" x-data="{ fechado: {{ $h && $h->closed ? 'true' : 'false' }} }">
                        <div class="w-24 text-sm font-medium text-gray-700">{{ $dia }}</div>
                        <label class="flex items-center gap-1 text-sm text-gray-500">
                            <input type="checkbox" name="horarios[{{ $i }}][closed]" value="1" x-model="fechado" class="rounded">
                            Fechado
                        </label>
                        <div x-show="!fechado" class="flex items-center gap-2">
                            <input type="time" name="horarios[{{ $i }}][open]" value="{{ $h?->open ?? '08:00' }}" class="text-sm border border-gray-300 rounded px-2 py-1">
                            <span class="text-gray-400 text-xs">até</span>
                            <input type="time" name="horarios[{{ $i }}][close]" value="{{ $h?->close ?? '18:00' }}" class="text-sm border border-gray-300 rounded px-2 py-1">
                        </div>
                    </div>
                @endforeach
                <div class="pt-3">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 rounded-lg">Salvar horários</button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
