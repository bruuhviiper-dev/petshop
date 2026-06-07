<x-app-layout>
    <x-slot name="title">Agenda — {{ $date->format('d/m/Y') }}</x-slot>

    <div x-data="agenda('{{ $date->format('Y-m-d') }}')" x-init="init()">

        {{-- Toolbar --}}
        <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
            <div class="flex items-center gap-2">
                <a href="{{ route('agenda.data', $date->copy()->subDay()->format('Y-m-d')) }}"
                   class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors text-gray-600 dark:text-gray-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200 min-w-32 text-center capitalize">
                    {{ $date->isoFormat('dddd, D [de] MMMM') }}
                    @if($date->isToday()) <span class="text-xs text-brand ml-1">(hoje)</span> @endif
                </span>
                <a href="{{ route('agenda.data', $date->copy()->addDay()->format('Y-m-d')) }}"
                   class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors text-gray-600 dark:text-gray-300">
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
                        @php
                            $horas = range(7, 20);
                            // Agendamentos sem colaborador (link público / "sem preferência").
                            // O groupBy converte a chave null em '' — buscamos ambos.
                            $semColab = $agendamentos->get('', $agendamentos->get(null, collect()));
                            $temSemColab = $semColab->isNotEmpty();
                            $colCount = max(1, $colaboradores->count()) + ($temSemColab ? 1 : 0);
                        @endphp
                        <div class="min-w-full">
                            {{-- Header colaboradores --}}
                            <div class="grid gap-1 mb-2 pb-2 border-b border-gray-100 dark:border-gray-700"
                                 style="grid-template-columns: 60px repeat({{ $colCount }}, minmax(120px, 1fr))">
                                <div></div>
                                @forelse($colaboradores as $col)
                                    <div class="text-center">
                                        <x-avatar :name="$col->user->name" class="mx-auto mb-1 w-8 h-8 text-xs" />
                                        <p class="text-xs font-medium text-gray-600 dark:text-gray-300 truncate">{{ $col->user->name }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $col->cargo }}</p>
                                    </div>
                                @empty
                                    <div class="text-center text-xs text-gray-400 dark:text-gray-500">Sem colaboradores</div>
                                @endforelse
                                @if($temSemColab)
                                    <div class="text-center">
                                        <div class="mx-auto mb-1 w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-300 flex items-center justify-center text-xs font-bold">?</div>
                                        <p class="text-xs font-medium text-gray-600 dark:text-gray-300 truncate">A definir</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">sem colaborador</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Slots de horário --}}
                            @foreach($horas as $hora)
                                @foreach([0, 30] as $minuto)
                                    @php $timeStr = sprintf('%02d:%02d', $hora, $minuto); @endphp
                                    <div class="grid gap-1 mb-0.5 group"
                                         style="grid-template-columns: 60px repeat({{ $colCount }}, minmax(120px, 1fr))">
                                        <div class="text-xs text-gray-400 dark:text-gray-500 text-right pr-2 pt-1">{{ $timeStr }}</div>
                                        @forelse($colaboradores as $col)
                                            @php
                                                $ags = $agendamentos->get($col->id, collect())
                                                    ->filter(fn($a) => $a->scheduled_at->format('H:i') === $timeStr);
                                            @endphp
                                            <div class="min-h-[40px] rounded border border-dashed border-gray-200 dark:border-gray-700 hover:border-brand hover:bg-brand/5 cursor-pointer transition-colors relative p-0.5"
                                                 @click="openNewModal('{{ $timeStr }}', {{ $col->id }})">
                                                @foreach($ags as $ag)
                                                    @include('agenda._card')
                                                @endforeach
                                            </div>
                                        @empty
                                            <div class="min-h-[40px] rounded border border-dashed border-gray-200 dark:border-gray-700 hover:border-brand hover:bg-brand/5 cursor-pointer transition-colors"
                                                 @click="openNewModal('{{ $timeStr }}', null)"></div>
                                        @endforelse

                                        {{-- Coluna "A definir": agendamentos sem colaborador (ex.: link público) --}}
                                        @if($temSemColab)
                                            @php $agsSem = $semColab->filter(fn($a) => $a->scheduled_at->format('H:i') === $timeStr); @endphp
                                            <div class="min-h-[40px] rounded border border-dashed border-amber-200 dark:border-amber-800/60 hover:border-brand hover:bg-brand/5 cursor-pointer transition-colors relative p-0.5"
                                                 @click="openNewModal('{{ $timeStr }}', null)">
                                                @foreach($agsSem as $ag)
                                                    @include('agenda._card')
                                                @endforeach
                                            </div>
                                        @endif
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
                        <div class="flex items-start gap-2 py-2 border-b border-gray-50 dark:border-gray-700 last:border-0">
                            <div class="text-xs font-bold text-brand w-10 shrink-0 pt-0.5">
                                {{ $ag->scheduled_at->format('H:i') }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-800 dark:text-gray-100 truncate">{{ $ag->pet?->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $ag->servico?->name }}</p>
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
                        <label class="form-label">Cliente *</label>
                        <input type="text" x-model="busca" @input.debounce.300ms="pesquisar()"
                               x-show="!clienteSelecionado"
                               class="form-input" placeholder="Buscar por nome ou telefone...">
                        <div x-show="resultados.length > 0 && !clienteSelecionado" class="mt-1 border border-gray-200 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 max-h-40 overflow-y-auto">
                            <template x-for="c in resultados" :key="c.id">
                                <button type="button" @click="selecionar(c)"
                                        class="w-full text-left px-3 py-2 text-sm text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-50 dark:border-gray-600 last:border-0">
                                    <span x-text="c.name" class="font-medium"></span>
                                    <span x-text="' · ' + c.phone" class="text-gray-500 dark:text-gray-400"></span>
                                </button>
                            </template>
                        </div>
                        <div x-show="busca.length >= 2 && resultados.length === 0 && !clienteSelecionado" class="form-hint mt-1">Nenhum cliente encontrado.</div>
                        <div x-show="clienteSelecionado" class="mt-1 flex items-center gap-2 p-2 bg-green-50 dark:bg-green-900/30 rounded-lg border border-green-200 dark:border-green-700">
                            <span class="text-sm text-green-800 dark:text-green-200 font-medium" x-text="clienteSelecionado?.name"></span>
                            <button type="button" @click="limpar()" class="ml-auto text-green-600 dark:text-green-300 hover:text-green-800">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            <input type="hidden" name="cliente_id" :value="clienteSelecionado?.id">
                        </div>
                    </div>

                    <div x-show="clienteSelecionado && pets.length > 0">
                        <label class="form-label">Pet *</label>
                        <select name="pet_id" class="form-select">
                            <option value="">Selecione o pet</option>
                            <template x-for="p in pets" :key="p.id">
                                <option :value="p.id" x-text="p.name + ' (' + p.species + ')'"></option>
                            </template>
                        </select>
                    </div>
                    <div x-show="clienteSelecionado && pets.length === 0" class="form-hint">
                        Este cliente ainda não tem pets cadastrados.
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Serviço *</label>
                        <select name="servico_id" class="form-select" required>
                            <option value="">Selecione</option>
                            @foreach($servicos as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} — R$ {{ number_format($s->price, 2, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Colaborador</label>
                        <select name="colaborador_id" x-model="colaboradorSelecionado" class="form-select">
                            <option value="">Sem preferência</option>
                            @foreach($colaboradores as $col)
                                <option value="{{ $col->id }}">{{ $col->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label">Data e hora *</label>
                    <input type="datetime-local" name="scheduled_at" x-model="scheduledAt" class="form-input" required>
                </div>

                {{-- Recorrência (banho/tosa recorrente) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Repetir</label>
                        <select name="recorrencia" x-model="recorrencia" class="form-select">
                            <option value="none">Não repetir</option>
                            <option value="weekly">Semanalmente</option>
                            <option value="biweekly">A cada 15 dias</option>
                            <option value="monthly">Mensalmente</option>
                        </select>
                    </div>
                    <div x-show="recorrencia !== 'none'" x-cloak>
                        <label class="form-label">Quantas vezes</label>
                        <input type="number" name="repeticoes" x-model="repeticoes" min="1" max="52" class="form-input">
                        <p class="form-hint mt-1">Cria a série de agendamentos automaticamente.</p>
                    </div>
                </div>

                <div>
                    <label class="form-label">Observações</label>
                    <textarea name="notes" rows="2" class="form-textarea resize-none"></textarea>
                </div>

                {{-- Erros de validação --}}
                <div x-show="erros.length" x-cloak class="rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-3 py-2">
                    <ul class="form-error list-disc list-inside space-y-0.5">
                        <template x-for="msg in erros" :key="msg"><li x-text="msg"></li></template>
                    </ul>
                </div>

                <div class="flex gap-3 justify-end pt-2">
                    <button type="button" @click="$dispatch('close-modal', 'novo-agendamento')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">Cancelar</button>
                    <button type="submit" :disabled="salvando"
                            class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 disabled:opacity-50 rounded-lg">
                        <span x-show="!salvando">Agendar</span>
                        <span x-show="salvando">Salvando…</span>
                    </button>
                </div>
            </form>
        </x-modal>

        {{-- Modal detalhe / atribuição de agendamento --}}
        <x-modal id="detalhe-agendamento" title="Agendamento" maxWidth="md">
            <template x-if="selecionado">
                <div class="space-y-4">
                    {{-- Resumo --}}
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-700/40 p-3 space-y-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Pet</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100" x-text="selecionado.pet || '—'"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Cliente</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100" x-text="selecionado.cliente || '—'"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Serviço</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100" x-text="selecionado.servico || '—'"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Quando</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100" x-text="(selecionado.data || '') + ' · ' + (selecionado.hora || '')"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Valor</span>
                            <span class="font-semibold text-brand" x-text="selecionado.valor"></span>
                        </div>
                    </div>

                    {{-- Atribuir colaborador --}}
                    <div>
                        <label class="form-label">Colaborador responsável</label>
                        <select x-model="selecionado.colaborador_id" class="form-select">
                            <option :value="null">A definir</option>
                            @foreach($colaboradores as $col)
                                <option :value="{{ $col->id }}">{{ $col->user->name }} — {{ $col->cargo }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="form-label">Status</label>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="st in statusList" :key="st.value">
                                <button type="button" @click="selecionado.status = st.value"
                                        :class="selecionado.status === st.value ? st.active : st.idle"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors"
                                        x-text="st.label"></button>
                            </template>
                        </div>
                    </div>

                    <div x-show="erros.length" x-cloak class="rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-3 py-2">
                        <ul class="form-error list-disc list-inside space-y-0.5">
                            <template x-for="msg in erros" :key="msg"><li x-text="msg"></li></template>
                        </ul>
                    </div>

                    <div class="flex gap-3 justify-end pt-1">
                        <button type="button" @click="$dispatch('close-modal', 'detalhe-agendamento')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">Fechar</button>
                        <button type="button" @click="salvarDetalhe()" :disabled="salvando"
                                class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 disabled:opacity-50 rounded-lg">
                            <span x-show="!salvando">Salvar</span>
                            <span x-show="salvando">Salvando…</span>
                        </button>
                    </div>
                </div>
            </template>
        </x-modal>
    </div>

    <script>
    function agenda(dataAtual) {
        return {
            scheduledAt: '',
            colaboradorSelecionado: '',
            recorrencia: 'none',
            repeticoes: 4,
            salvando: false,
            erros: [],
            selecionado: null,
            statusList: [
                { value: 'pendente',     label: 'Pendente',     active: 'bg-amber-500 text-white border-amber-500',   idle: 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800' },
                { value: 'confirmado',   label: 'Confirmado',   active: 'bg-blue-600 text-white border-blue-600',     idle: 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800' },
                { value: 'em_andamento', label: 'Em andamento', active: 'bg-purple-600 text-white border-purple-600', idle: 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800' },
                { value: 'concluido',    label: 'Concluído',    active: 'bg-green-600 text-white border-green-600',   idle: 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 border-green-200 dark:border-green-800' },
                { value: 'cancelado',    label: 'Cancelado',    active: 'bg-gray-500 text-white border-gray-500',     idle: 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600' },
            ],
            init() {
                this.scheduledAt = dataAtual + 'T08:00';
            },
            openNewModal(hora, colaboradorId) {
                this.scheduledAt = dataAtual + 'T' + hora;
                this.colaboradorSelecionado = colaboradorId ?? '';
                this.recorrencia = 'none';
                this.repeticoes = 4;
                this.erros = [];
                this.$dispatch('open-modal', 'novo-agendamento');
            },
            editAgendamento(ag) {
                // Normaliza colaborador_id para string (compatível com o <select>).
                this.selecionado = { ...ag, colaborador_id: ag.colaborador_id ?? null };
                this.erros = [];
                this.$dispatch('open-modal', 'detalhe-agendamento');
            },
            async salvarDetalhe() {
                if (this.salvando || !this.selecionado) return;
                this.salvando = true;
                this.erros = [];
                try {
                    const resp = await fetch('{{ url('agenda') }}/' + this.selecionado.id + '/status', {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify({
                            status: this.selecionado.status,
                            colaborador_id: this.selecionado.colaborador_id || null,
                        }),
                    });
                    const json = await resp.json().catch(() => ({}));
                    if (resp.ok && json.success) {
                        sessionStorage.setItem('flash', JSON.stringify({ type: 'success', text: 'Agendamento atualizado!' }));
                        this.$dispatch('close-modal', 'detalhe-agendamento');
                        window.location.reload();
                        return;
                    }
                    this.erros = resp.status === 422 && json.errors
                        ? Object.values(json.errors).flat()
                        : [json.message || 'Não foi possível atualizar o agendamento.'];
                } catch (e) {
                    this.erros = ['Erro de conexão. Tente novamente.'];
                } finally {
                    this.salvando = false;
                }
            },
            async salvarAgendamento(e) {
                if (this.salvando) return;
                this.salvando = true;
                this.erros = [];
                const form = e.target;
                const data = Object.fromEntries(new FormData(form).entries());
                try {
                    const resp = await fetch('{{ route('agenda.store') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify(data),
                    });
                    const json = await resp.json().catch(() => ({}));
                    if (resp.ok && json.success) {
                        // Fecha o modal, guarda o toast e recarrega para exibir o novo card.
                        const txt = (json.total > 1) ? (json.total + ' agendamentos recorrentes criados!') : 'Agendamento criado com sucesso!';
                        sessionStorage.setItem('flash', JSON.stringify({ type: 'success', text: txt }));
                        this.$dispatch('close-modal', 'novo-agendamento');
                        window.location.reload();
                        return;
                    }
                    if (resp.status === 422 && json.errors) {
                        this.erros = Object.values(json.errors).flat();
                    } else {
                        this.erros = [json.message || 'Não foi possível criar o agendamento. Verifique os campos.'];
                    }
                } catch (err) {
                    this.erros = ['Erro de conexão. Tente novamente.'];
                } finally {
                    this.salvando = false;
                }
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
