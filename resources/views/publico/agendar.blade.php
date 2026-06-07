<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Agendar — {{ $petshop->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --color-brand: {{ $petshop->primary_color }}; }
        .bg-brand { background-color: var(--color-brand); }
        .text-brand { color: var(--color-brand); }
        .border-brand { border-color: var(--color-brand); }
        .ring-brand { --tw-ring-color: var(--color-brand); }
        .bg-brand-soft { background-color: color-mix(in srgb, var(--color-brand) 8%, transparent); }
        .focus\:border-brand:focus { border-color: var(--color-brand); }
        .focus\:ring-brand\/20:focus { --tw-ring-color: color-mix(in srgb, var(--color-brand) 20%, transparent); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased">

    <header class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-20">
        <div class="max-w-lg mx-auto px-4 py-3 flex items-center gap-3">
            @if($petshop->logo)
                <img src="{{ Storage::url($petshop->logo) }}" alt="{{ $petshop->name }}" class="h-10 w-auto">
            @else
                <div class="w-10 h-10 rounded-full bg-brand flex items-center justify-center text-white font-bold">{{ substr($petshop->name, 0, 1) }}</div>
            @endif
            <div>
                <h1 class="font-bold text-gray-800 leading-tight">{{ $petshop->name }}</h1>
                <p class="text-xs text-gray-500">Agendamento online</p>
            </div>
        </div>
    </header>

    <main class="max-w-lg mx-auto px-4 py-6"
          x-data="agendamentoPublico('{{ $petshop->slug }}', {{ $servicos->toJson() }})">

        {{-- Honeypot anti-bot --}}
        <div style="display:none" aria-hidden="true">
            <input type="text" name="website" class="hidden" autocomplete="off" tabindex="-1" x-model="form.website">
        </div>

        {{-- Stepper --}}
        <div x-show="etapa <= 3" class="flex items-center justify-center gap-1.5 mb-5">
            @foreach(['Serviço', 'Horário', 'Seus dados'] as $i => $label)
                <template x-if="true">
                    <div class="flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold transition-colors"
                             :class="etapa >= {{ $i + 1 }} ? 'bg-brand text-white' : 'bg-gray-200 text-gray-500'">{{ $i + 1 }}</div>
                        <span class="text-xs font-medium hidden sm:inline" :class="etapa >= {{ $i + 1 }} ? 'text-brand' : 'text-gray-400'">{{ $label }}</span>
                        @if($i < 2)<div class="w-6 h-px bg-gray-200"></div>@endif
                    </div>
                </template>
            @endforeach
        </div>

        {{-- Resumo fixo da escolha (etapas 2 e 3) --}}
        <div x-show="etapa === 2 || etapa === 3" x-cloak class="mb-4 flex items-center gap-3 rounded-xl bg-brand-soft border border-brand/20 px-3 py-2 text-sm">
            <template x-if="servicoSelecionado">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 truncate" x-text="servicoSelecionado.name"></p>
                    <p class="text-xs text-gray-500">
                        <span x-text="servicoSelecionado.duration_minutes + ' min · ' + precoFmt(servicoSelecionado.price)"></span>
                        <span x-show="form.data" x-text="' · ' + dataFmt + (form.horario ? ' às ' + form.horario : '')"></span>
                    </p>
                </div>
            </template>
            <button @click="etapa = 1" class="text-xs text-brand font-medium shrink-0">Alterar</button>
        </div>

        {{-- ETAPA 1 — Serviço --}}
        <div x-show="etapa === 1" x-transition>
            <h2 class="text-base font-semibold text-gray-800 mb-3">O que seu pet precisa?</h2>
            <div class="grid grid-cols-1 gap-2.5">
                <template x-for="s in servicos" :key="s.id">
                    <button type="button" @click="escolherServico(s)"
                            class="w-full p-4 border-2 border-gray-200 rounded-xl text-left transition-all hover:border-brand hover:bg-brand-soft flex items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-800" x-text="s.name"></p>
                            <p class="text-sm text-gray-500 mt-0.5" x-text="s.duration_minutes + ' min'"></p>
                        </div>
                        <span class="text-base font-bold text-brand shrink-0" x-text="precoFmt(s.price)"></span>
                    </button>
                </template>
                <p x-show="servicos.length === 0" class="text-sm text-gray-400 text-center py-6">Nenhum serviço disponível no momento.</p>
            </div>
        </div>

        {{-- ETAPA 2 — Data e horário --}}
        <div x-show="etapa === 2" x-cloak x-transition>
            <h2 class="text-base font-semibold text-gray-800 mb-3">Escolha o melhor dia e horário</h2>

            {{-- Atalhos de dia --}}
            <div class="flex gap-2 mb-3 overflow-x-auto pb-1">
                <template x-for="d in proximosDias" :key="d.value">
                    <button type="button" @click="form.data = d.value; carregarSlots()"
                            :class="form.data === d.value ? 'bg-brand text-white border-brand' : 'bg-white text-gray-600 border-gray-200'"
                            class="shrink-0 px-3 py-2 rounded-xl border text-center transition-colors">
                        <span class="block text-[10px] uppercase" x-text="d.weekday"></span>
                        <span class="block text-sm font-bold" x-text="d.day"></span>
                    </button>
                </template>
            </div>

            <input type="date" x-model="form.data" @change="carregarSlots()" :min="hoje"
                   class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 mb-4 focus:ring-2 focus:ring-brand/20 focus:border-brand">

            {{-- Carregando --}}
            <div x-show="carregandoSlots" class="flex items-center justify-center gap-2 py-8 text-sm text-gray-400">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Buscando horários…
            </div>

            {{-- Slots agrupados por período --}}
            <template x-if="!carregandoSlots && slots.length > 0">
                <div class="space-y-4">
                    <template x-for="periodo in periodos" :key="periodo.label">
                        <div x-show="slotsPorPeriodo[periodo.key].length > 0">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2" x-text="periodo.label"></p>
                            <div class="grid grid-cols-4 gap-2">
                                <template x-for="slot in slotsPorPeriodo[periodo.key]" :key="slot">
                                    <button type="button" @click="form.horario = slot; etapa = 3"
                                            :class="form.horario === slot ? 'bg-brand text-white' : 'bg-white text-gray-700 border border-gray-200 hover:border-brand'"
                                            class="py-2 text-sm font-medium rounded-lg transition-colors" x-text="slot"></button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <div x-show="!carregandoSlots && form.data && slots.length === 0" class="text-center py-8">
                <p class="text-sm text-gray-400">Sem horários para esta data. 🗓️</p>
                <p class="text-xs text-gray-400 mt-1">Tente outro dia acima.</p>
            </div>

            <button @click="etapa = 1" class="mt-4 text-sm text-gray-500 hover:text-gray-700">← Voltar aos serviços</button>
        </div>

        {{-- ETAPA 3 — Dados do tutor e pet --}}
        <div x-show="etapa === 3" x-cloak x-transition>
            <h2 class="text-base font-semibold text-gray-800 mb-3">Quase lá! Seus dados</h2>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Seu nome *</label>
                        <input type="text" x-model="form.dono_nome" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand" placeholder="Nome completo">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">WhatsApp *</label>
                        <input type="tel" x-model="form.dono_telefone" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand" placeholder="(11) 99999-0000">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nome do pet *</label>
                        <input type="text" x-model="form.pet_nome" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Espécie *</label>
                        <select x-model="form.pet_especie" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                            <option value="">Selecione</option><option>Cachorro</option><option>Gato</option><option value="Outro">Outro</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Raça (opcional)</label>
                    <input type="text" x-model="form.pet_raca" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                </div>

                <p x-show="erro" x-cloak x-text="erro" class="text-xs text-red-500 text-center"></p>

                <div class="flex gap-3">
                    <button @click="etapa = 2" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl">← Voltar</button>
                    <button @click="confirmarAgendamento()" :disabled="carregando"
                            class="flex-1 py-2.5 bg-brand text-white text-sm font-semibold rounded-xl hover:opacity-90 disabled:opacity-50 transition-opacity">
                        <span x-show="!carregando">Confirmar agendamento</span>
                        <span x-show="carregando">Agendando…</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- CONFIRMAÇÃO --}}
        <div x-show="etapa === 4" x-cloak x-transition>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800 mb-1">Agendado! 🎉</h2>
                <p class="text-sm text-gray-500 mb-4">Seu horário está reservado.</p>
                <template x-if="confirmacao">
                    <div class="bg-gray-50 rounded-xl p-4 text-left text-sm space-y-2 mb-4">
                        <div class="flex justify-between"><span class="text-gray-500">Pet</span><span class="font-medium" x-text="confirmacao.pet"></span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Serviço</span><span class="font-medium" x-text="confirmacao.servico"></span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Data/Hora</span><span class="font-medium" x-text="confirmacao.scheduled_at"></span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Valor</span><span class="font-semibold text-brand" x-text="confirmacao.valor"></span></div>
                    </div>
                </template>
                <p class="text-xs text-gray-400 mb-4">Você receberá a confirmação pelo WhatsApp. 📲</p>
                @if($petshop->phone)
                    <a :href="'https://wa.me/55' + '{{ preg_replace('/\D/', '', $petshop->phone) }}'" target="_blank"
                       class="block w-full py-2.5 mb-2 bg-green-500 text-white text-sm font-semibold rounded-xl hover:opacity-90">Falar com o petshop no WhatsApp</a>
                @endif
                <button @click="reiniciar()" class="text-sm text-brand hover:underline">Fazer outro agendamento</button>
            </div>
        </div>
    </main>

    <script>
    function agendamentoPublico(slug, servicos) {
        return {
            etapa: 1,
            servicos,
            slots: [],
            carregando: false,
            carregandoSlots: false,
            erro: '',
            confirmacao: null,
            hoje: new Date().toISOString().split('T')[0],
            periodos: [
                { key: 'manha', label: 'Manhã' },
                { key: 'tarde', label: 'Tarde' },
                { key: 'noite', label: 'Noite' },
            ],
            form: {
                dono_nome: '', dono_telefone: '', pet_nome: '', pet_especie: '', pet_raca: '',
                servico_id: null, data: '', horario: '', website: '',
            },
            get servicoSelecionado() { return this.servicos.find(s => s.id === this.form.servico_id) || null; },
            get dataFmt() {
                if (!this.form.data) return '';
                const [y, m, d] = this.form.data.split('-');
                return `${d}/${m}`;
            },
            get proximosDias() {
                const wd = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];
                const arr = [];
                for (let i = 0; i < 7; i++) {
                    const dt = new Date(); dt.setDate(dt.getDate() + i);
                    arr.push({ value: dt.toISOString().split('T')[0], weekday: i === 0 ? 'Hoje' : wd[dt.getDay()], day: dt.getDate() });
                }
                return arr;
            },
            get slotsPorPeriodo() {
                const g = { manha: [], tarde: [], noite: [] };
                this.slots.forEach(s => {
                    const h = parseInt(s.split(':')[0], 10);
                    if (h < 12) g.manha.push(s); else if (h < 18) g.tarde.push(s); else g.noite.push(s);
                });
                return g;
            },
            precoFmt(v) { return 'R$ ' + parseFloat(v).toFixed(2).replace('.', ','); },
            escolherServico(s) {
                this.form.servico_id = s.id;
                this.etapa = 2;
                if (this.form.data) this.carregarSlots();
            },
            async carregarSlots() {
                if (!this.form.data || !this.form.servico_id) return;
                this.slots = []; this.form.horario = ''; this.carregandoSlots = true;
                try {
                    const r = await fetch(`/agendar/${slug}/horarios?data=${this.form.data}&servico_id=${this.form.servico_id}`);
                    const data = await r.json();
                    this.slots = data.slots ?? [];
                } catch (e) { this.erro = 'Não foi possível carregar os horários.'; }
                finally { this.carregandoSlots = false; }
            },
            async confirmarAgendamento() {
                this.erro = '';
                if (!this.form.dono_nome || !this.form.dono_telefone || !this.form.pet_nome || !this.form.pet_especie) {
                    this.erro = 'Preencha nome, WhatsApp, nome e espécie do pet.'; return;
                }
                if (!this.form.horario) { this.erro = 'Volte e escolha um horário.'; return; }
                this.carregando = true;
                try {
                    const r = await fetch(`/agendar/${slug}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? '', 'Accept': 'application/json' },
                        body: JSON.stringify(this.form),
                    });
                    const data = await r.json();
                    if (data.success) { this.confirmacao = data.agendamento; this.etapa = 4; }
                    else { this.erro = data.message ?? 'Erro ao agendar. Tente novamente.'; }
                } catch { this.erro = 'Erro de conexão. Tente novamente.'; }
                finally { this.carregando = false; }
            },
            reiniciar() {
                this.etapa = 1; this.confirmacao = null; this.slots = [];
                this.form = { dono_nome: '', dono_telefone: '', pet_nome: '', pet_especie: '', pet_raca: '', servico_id: null, data: '', horario: '', website: '' };
            },
        };
    }
    </script>
</body>
</html>
