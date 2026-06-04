<x-app-layout>
    <x-slot name="title">Agenda — {{ $date->format('d/m/Y') }}</x-slot>

    <div x-data="agenda('{{ $date->format('Y-m-d') }}')" x-init="init()">

        {{-- Toolbar --}}
        <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
            <div class="flex items-center gap-2">
                <a href="{{ route('agenda.data', $date->copy()->subDay()->format('Y-m-d')) }}"
                   class="p-2 rounded-lg hover:bg-gray-200 transition-colors text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <span class="text-sm font-semibold text-gray-700 min-w-32 text-center">
                    {{ $date->isoFormat('dddd, D [de] MMMM') }}
                    @if($date->isToday()) <span class="text-xs text-brand ml-1">(hoje)</span> @endif
                </span>
                <a href="{{ route('agenda.data', $date->copy()->addDay()->format('Y-m-d')) }}"
                   class="p-2 rounded-lg hover:bg-gray-200 transition-colors text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('agenda.index') }}" class="ml-2 text-xs text-brand hover:underline">Hoje</a>
            </div>
            <button @click="$dispatch('open-modal', 'novo-agendamento')"
                    class="flex items-center gap-2 px-4 py-2 bg-brand text-white text-sm font-medium rounded-lg hover:opacity-90 transition-opacity">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Novo agendamento
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">

            {{-- Grade principal --}}
            <div class="lg:col-span-3">
                <x-card>
                    <div class="overflow-x-auto">
                        @php $horas = range(7, 20); @endphp
                        <div class="min-w-full">
                            {{-- Header colaboradores --}}
                            <div class="grid gap-1 mb-2 pb-2 border-b border-gray-100"
                                 style="grid-template-columns: 60px repeat({{ max(1, $colaboradores->count()) }}, 1fr)">
                                <div></div>
                                @forelse($colaboradores as $col)
                                    <div class="text-center">
                                        <x-avatar :name="$col->user->name" class="mx-auto mb-1 w-8 h-8 text-xs" />
                                        <p class="text-xs font-medium text-gray-600 truncate">{{ $col->user->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $col->cargo }}</p>
                                    </div>
                                @empty
                                    <div class="text-center text-xs text-gray-400">Sem colaboradores</div>
                                @endforelse
                            </div>

                            {{-- Slots de horário --}}
                            @foreach($horas as $hora)
                                @foreach([0, 30] as $minuto)
                                    @php $timeStr = sprintf('%02d:%02d', $hora, $minuto); @endphp
                                    <div class="grid gap-1 mb-0.5 group"
                                         style="grid-template-columns: 60px repeat({{ max(1, $colaboradores->count()) }}, 1fr)">
                                        <div class="text-xs text-gray-400 text-right pr-2 pt-1">{{ $timeStr }}</div>
                                        @forelse($colaboradores as $col)
                                            @php
                                                $ags = $agendamentos->get($col->id, collect())
                                                    ->filter(fn($a) => $a->scheduled_at->format('H:i') === $timeStr);
                                            @endphp
                                            <div class="min-h-[40px] rounded border border-dashed border-gray-200 hover:border-brand hover:bg-brand/5 cursor-pointer transition-colors relative p-0.5"
                                                 @click="openNewModal('{{ $timeStr }}', {{ $col->id }})">
                                                @foreach($ags as $ag)
                                                    <div class="rounded p-1.5 text-xs cursor-pointer mb-0.5
                                                        {{ $ag->status === 'pendente' ? 'bg-amber-100 text-amber-800' : '' }}
                                                        {{ $ag->status === 'confirmado' ? 'bg-blue-100 text-blue-800' : '' }}
                                                        {{ $ag->status === 'em_andamento' ? 'bg-purple-100 text-purple-800' : '' }}
                                                        {{ $ag->status === 'concluido' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $ag->status === 'cancelado' ? 'bg-gray-100 text-gray-500 line-through' : '' }}"
                                                         @click.stop="editAgendamento({{ $ag->id }})">
                                                        <p class="font-medium truncate">{{ $ag->pet?->name }}</p>
                                                        <p class="truncate opacity-75">{{ $ag->servico?->name }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @empty
                                            <div class="min-h-[40px] rounded border border-dashed border-gray-200 hover:border-brand hover:bg-brand/5 cursor-pointer transition-colors"
                                                 @click="openNewModal('{{ $timeStr }}', null)"></div>
                                        @endforelse
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </x-card>
            </div>

            {{-- Sidebar com próximos 5 --}}
            <div class="space-y-3">
                <x-card title="Próximos hoje">
                    @forelse($proximosCinco as $ag)
                        <div class="flex items-start gap-2 py-2 border-b border-gray-50 last:border-0">
                            <div class="text-xs font-bold text-brand w-10 shrink-0 pt-0.5">
                                {{ $ag->scheduled_at->format('H:i') }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-800 truncate">{{ $ag->pet?->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $ag->servico?->name }}</p>
                                <x-badge :status="$ag->status" class="mt-0.5" />
                            </div>
                        </div>
                    @empty
                        <x-empty-state icon="calendar" title="Sem agendamentos" description="Nenhum agendamento pendente para hoje." />
                    @endforelse
                </x-card>
            </div>
        </div>

        {{-- Modal novo agendamento --}}
        <x-modal id="novo-agendamento" title="Novo Agendamento" maxWidth="lg">
            <form @submit.prevent="salvarAgendamento" class="space-y-4">
                {{-- Busca cliente --}}
                <div x-data="clienteBusca()" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Cliente</label>
                        <input type="text" x-model="busca" @input.debounce.300ms="pesquisar()"
                               class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand"
                               placeholder="Nome ou telefone...">
                        <div x-show="resultados.length > 0 && !clienteSelecionado" class="mt-1 border border-gray-200 rounded-lg shadow-sm bg-white max-h-40 overflow-y-auto">
                            <template x-for="c in resultados" :key="c.id">
                                <button type="button" @click="selecionar(c)"
                                        class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 border-b border-gray-50 last:border-0">
                                    <span x-text="c.name" class="font-medium"></span>
                                    <span x-text="' · ' + c.phone" class="text-gray-500"></span>
                                </button>
                            </template>
                        </div>
                        <div x-show="clienteSelecionado" class="mt-1 flex items-center gap-2 p-2 bg-green-50 rounded-lg border border-green-200">
                            <span class="text-sm text-green-800 font-medium" x-text="clienteSelecionado?.name"></span>
                            <button type="button" @click="limpar()" class="ml-auto text-green-600 hover:text-green-800">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            <input type="hidden" name="cliente_id" :value="clienteSelecionado?.id">
                        </div>
                    </div>

                    <div x-show="clienteSelecionado && pets.length > 0">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Pet</label>
                        <select name="pet_id" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                            <option value="">Selecione o pet</option>
                            <template x-for="p in pets" :key="p.id">
                                <option :value="p.id" x-text="p.name + ' (' + p.species + ')'"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Serviço</label>
                        <select name="servico_id" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand" required>
                            <option value="">Selecione</option>
                            @foreach($servicos as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} — R$ {{ number_format($s->price, 2, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Colaborador</label>
                        <select name="colaborador_id" x-model="colaboradorSelecionado" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                            <option value="">Sem preferência</option>
                            @foreach($colaboradores as $col)
                                <option value="{{ $col->id }}">{{ $col->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Data e hora</label>
                    <input type="datetime-local" name="scheduled_at" x-model="scheduledAt"
                           class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand" required>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Observações</label>
                    <textarea name="notes" rows="2" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand resize-none"></textarea>
                </div>

                <div class="flex gap-3 justify-end pt-2">
                    <button type="button" @click="$dispatch('close-modal', 'novo-agendamento')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 rounded-lg">Agendar</button>
                </div>
            </form>
        </x-modal>
    </div>

    <script>
    function agenda(dataAtual) {
        return {
            scheduledAt: '',
            colaboradorSelecionado: '',
            init() {
                this.scheduledAt = dataAtual + 'T08:00';
            },
            openNewModal(hora, colaboradorId) {
                this.scheduledAt = dataAtual + 'T' + hora;
                this.colaboradorSelecionado = colaboradorId ?? '';
                this.$dispatch('open-modal', 'novo-agendamento');
            },
            editAgendamento(id) {
                window.location.href = '#agendamento-' + id;
            },
            async salvarAgendamento(e) {
                const form = e.target;
                const data = Object.fromEntries(new FormData(form).entries());
                const resp = await fetch('{{ route('agenda.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify(data),
                });
                if (resp.ok) { window.location.reload(); }
            }
        };
    }

    function clienteBusca() {
        return {
            busca: '', resultados: [], clienteSelecionado: null, pets: [],
            async pesquisar() {
                if (this.busca.length < 2) { this.resultados = []; return; }
                const r = await fetch('/clientes/buscar?q=' + encodeURIComponent(this.busca));
                this.resultados = await r.json();
            },
            selecionar(c) { this.clienteSelecionado = c; this.pets = c.pets; this.resultados = []; },
            limpar() { this.clienteSelecionado = null; this.pets = []; this.busca = ''; },
        };
    }
    </script>
</x-app-layout>
