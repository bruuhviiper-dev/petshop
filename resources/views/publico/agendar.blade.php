<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar — {{ $petshop->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --color-brand: {{ $petshop->primary_color }}; }
        .bg-brand { background-color: var(--color-brand); }
        .text-brand { color: var(--color-brand); }
        .border-brand { border-color: var(--color-brand); }
        .ring-brand { --tw-ring-color: var(--color-brand); }
        .focus\:border-brand:focus { border-color: var(--color-brand); }
        .focus\:ring-brand\/20:focus { --tw-ring-color: color-mix(in srgb, var(--color-brand) 20%, transparent); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased">

    <header class="bg-white border-b border-gray-100 shadow-sm">
        <div class="max-w-lg mx-auto px-4 py-4 flex items-center gap-3">
            @if($petshop->logo)
                <img src="{{ Storage::url($petshop->logo) }}" alt="{{ $petshop->name }}" class="h-10 w-auto">
            @else
                <div class="w-10 h-10 rounded-full bg-brand flex items-center justify-center text-white font-bold">
                    {{ substr($petshop->name, 0, 1) }}
                </div>
            @endif
            <div>
                <h1 class="font-bold text-gray-800">{{ $petshop->name }}</h1>
                <p class="text-xs text-gray-500">Agendamento online</p>
            </div>
        </div>
    </header>

    <main class="max-w-lg mx-auto px-4 py-8"
          x-data="agendamentoPublico('{{ $petshop->slug }}', {{ $servicos->toJson() }})">

        {{-- Passos --}}
        <div class="flex items-center justify-center gap-2 mb-8">
            @foreach(['Dados', 'Serviço', 'Horário'] as $i => $label)
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-colors"
                         :class="etapa >= {{ $i + 1 }} ? 'bg-brand text-white' : 'bg-gray-200 text-gray-500'">
                        {{ $i + 1 }}
                    </div>
                    <span class="text-xs font-medium" :class="etapa >= {{ $i + 1 }} ? 'text-brand' : 'text-gray-400'">{{ $label }}</span>
                    @if($i < 2) <div class="w-8 h-px bg-gray-200"></div> @endif
                </div>
            @endforeach
        </div>

        {{-- Etapa 1: Dados do dono e pet --}}
        <div x-show="etapa === 1" x-transition>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-800">Seus dados</h2>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Seu nome *</label>
                    <input type="text" x-model="form.dono_nome" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand" placeholder="Nome completo">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Telefone / WhatsApp *</label>
                    <input type="tel" x-model="form.dono_telefone" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand" placeholder="(11) 99999-0000">
                </div>
                <h2 class="text-base font-semibold text-gray-800 pt-2">Dados do pet</h2>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nome do pet *</label>
                        <input type="text" x-model="form.pet_nome" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Espécie *</label>
                        <select x-model="form.pet_especie" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                            <option value="">Selecione</option>
                            <option value="Cachorro">Cachorro</option>
                            <option value="Gato">Gato</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Raça</label>
                    <input type="text" x-model="form.pet_raca" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                </div>
                <button @click="validarEtapa1()" class="w-full py-3 bg-brand text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity">
                    Continuar →
                </button>
                <p x-show="erro" x-text="erro" class="text-xs text-red-500 text-center"></p>
            </div>
        </div>

        {{-- Etapa 2: Serviço --}}
        <div x-show="etapa === 2" x-transition>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-semibold text-gray-800 mb-4">Escolha o serviço</h2>
                <div class="grid grid-cols-1 gap-3">
                    <template x-for="s in servicos" :key="s.id">
                        <button type="button" @click="form.servico_id = s.id; etapa = 3; carregarSlots()"
                                :class="form.servico_id === s.id ? 'border-brand bg-brand/5 ring-2 ring-brand/20' : 'border-gray-200 hover:border-brand hover:bg-gray-50'"
                                class="w-full p-4 border-2 rounded-xl text-left transition-colors">
                            <p class="font-semibold text-gray-800" x-text="s.name"></p>
                            <p class="text-sm text-gray-500 mt-0.5" x-text="s.duration_minutes + ' min · R$ ' + parseFloat(s.price).toFixed(2).replace('.', ',')"></p>
                        </button>
                    </template>
                </div>
                <button @click="etapa = 1" class="mt-4 text-sm text-gray-500 hover:text-gray-700">← Voltar</button>
            </div>
        </div>

        {{-- Etapa 3: Data e horário --}}
        <div x-show="etapa === 3" x-transition>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-800">Escolha a data e horário</h2>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Data *</label>
                    <input type="date" x-model="form.data" @change="carregarSlots()" :min="hoje"
                           class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                </div>
                <div x-show="slots.length > 0">
                    <label class="block text-xs font-medium text-gray-700 mb-2">Horário disponível *</label>
                    <div class="grid grid-cols-4 gap-2">
                        <template x-for="slot in slots" :key="slot">
                            <button type="button" @click="form.horario = slot"
                                    :class="form.horario === slot ? 'bg-brand text-white' : 'bg-gray-100 text-gray-700 hover:bg-brand/10'"
                                    class="py-2 text-sm font-medium rounded-lg transition-colors" x-text="slot"></button>
                        </template>
                    </div>
                </div>
                <div x-show="form.data && slots.length === 0" class="text-sm text-gray-400 text-center py-4">
                    Sem horários disponíveis para esta data.
                </div>
                <div class="flex gap-3">
                    <button @click="etapa = 2" class="flex-1 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl">← Voltar</button>
                    <button @click="confirmarAgendamento()" :disabled="!form.horario || carregando"
                            class="flex-1 py-2.5 bg-brand text-white text-sm font-semibold rounded-xl hover:opacity-90 disabled:opacity-50 transition-opacity">
                        <span x-show="!carregando">Confirmar agendamento</span>
                        <span x-show="carregando">Aguarde...</span>
                    </button>
                </div>
                <p x-show="erro" x-text="erro" class="text-xs text-red-500 text-center"></p>
            </div>
        </div>

        {{-- Confirmação --}}
        <div x-show="etapa === 4" x-transition>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800 mb-2">Agendado!</h2>
                <p class="text-sm text-gray-500 mb-4">Seu agendamento foi realizado com sucesso.</p>
                <template x-if="confirmacao">
                    <div class="bg-gray-50 rounded-xl p-4 text-left text-sm space-y-2 mb-4">
                        <div class="flex justify-between"><span class="text-gray-500">Pet:</span><span class="font-medium" x-text="confirmacao.pet"></span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Serviço:</span><span class="font-medium" x-text="confirmacao.servico"></span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Data/Hora:</span><span class="font-medium" x-text="confirmacao.scheduled_at"></span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Valor:</span><span class="font-semibold text-brand" x-text="confirmacao.valor"></span></div>
                    </div>
                </template>
                <p class="text-xs text-gray-400">Você receberá uma confirmação pelo WhatsApp.</p>
                <button @click="reiniciar()" class="mt-4 text-sm text-brand hover:underline">Fazer outro agendamento</button>
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
            erro: '',
            confirmacao: null,
            hoje: new Date().toISOString().split('T')[0],
            form: {
                dono_nome: '', dono_telefone: '', pet_nome: '', pet_especie: '', pet_raca: '',
                servico_id: null, data: '', horario: '',
            },
            validarEtapa1() {
                this.erro = '';
                if (!this.form.dono_nome || !this.form.dono_telefone || !this.form.pet_nome || !this.form.pet_especie) {
                    this.erro = 'Preencha todos os campos obrigatórios.'; return;
                }
                this.etapa = 2;
            },
            async carregarSlots() {
                if (!this.form.data || !this.form.servico_id) return;
                this.slots = [];
                this.form.horario = '';
                const url = `/agendar/${slug}/horarios?data=${this.form.data}&servico_id=${this.form.servico_id}`;
                const r = await fetch(url);
                const data = await r.json();
                this.slots = data.slots ?? [];
            },
            async confirmarAgendamento() {
                this.erro = '';
                if (!this.form.horario) { this.erro = 'Selecione um horário.'; return; }
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
                this.form = { dono_nome: '', dono_telefone: '', pet_nome: '', pet_especie: '', pet_raca: '', servico_id: null, data: '', horario: '' };
            },
        };
    }
    </script>
</body>
</html>
