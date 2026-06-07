<x-app-layout>
    <x-slot name="title">Configurações — Colaboradores</x-slot>
    @include('configuracoes._nav')

    <div class="max-w-2xl mt-4"
         x-data="colaboradoresPage({
            old: @js(old('_colab_id')),
            hasErrors: {{ $errors->any() ? 'true' : 'false' }}
         })">

        {{-- Erros de validação --}}
        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3">
                <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                    Equipe <span class="text-gray-400 dark:text-gray-500">({{ $colaboradores->count() }})</span>
                </h3>
                <button @click="abrirNovo()"
                        class="flex items-center gap-1.5 px-3 py-1.5 bg-brand text-white text-sm font-medium rounded-lg hover:opacity-90 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Novo colaborador
                </button>
            </div>

            <div class="space-y-2">
                @forelse($colaboradores as $col)
                    @php
                        $colPayload = [
                            'id'                  => $col->id,
                            'name'                => $col->user->name,
                            'email'               => $col->user->email,
                            'phone'               => $col->user->phone,
                            'cargo'               => $col->cargo,
                            'comissao_percentual' => (float) $col->comissao_percentual,
                        ];
                    @endphp
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/40">
                        <x-avatar :name="$col->user->name" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $col->user->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $col->cargo }} · {{ rtrim(rtrim(number_format($col->comissao_percentual, 2, ',', '.'), '0'), ',') }}% comissão
                                · {{ $col->agendamentos_count }} atend.
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $col->user->email }}</p>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <button class="text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-brand"
                                    @click="abrirEdicao(@js($colPayload))">
                                Editar
                            </button>
                            <x-confirm-delete :action="route('configuracoes.colaboradores.destroy', $col)" item-name="este colaborador">
                                <button type="button" class="text-xs font-medium text-red-500 hover:text-red-700">Excluir</button>
                            </x-confirm-delete>
                        </div>
                    </div>
                @empty
                    <x-empty-state icon="users" title="Nenhum colaborador" description="Adicione sua equipe para distribuir agendamentos e calcular comissões." />
                @endforelse
            </div>
        </x-card>

        {{-- Modal criar / editar --}}
        <div x-show="aberto" x-cloak
             class="fixed inset-0 z-50 flex items-start justify-center pt-10 px-4 pb-4"
             style="display:none">
            <div x-show="aberto" x-transition.opacity
                 @click="fechar()" class="fixed inset-0 bg-gray-900/50"></div>

            <div x-show="aberto"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg z-10 max-h-[90vh] flex flex-col">

                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100"
                        x-text="modo === 'novo' ? 'Novo colaborador' : 'Editar colaborador'"></h3>
                    <button @click="fechar()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" :action="actionUrl" class="p-5 overflow-y-auto space-y-4">
                    @csrf
                    <template x-if="modo === 'editar'"><input type="hidden" name="_method" value="PUT"></template>
                    <input type="hidden" name="_colab_id" :value="form.id">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Nome *</label>
                            <input type="text" name="name" x-model="form.name" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Cargo *</label>
                            <input type="text" name="cargo" x-model="form.cargo" required list="cargos" placeholder="Tosador, Banhista…" class="form-input">
                            <datalist id="cargos">
                                <option value="Tosador"></option>
                                <option value="Banhista"></option>
                                <option value="Veterinário"></option>
                                <option value="Auxiliar"></option>
                                <option value="Atendente"></option>
                            </datalist>
                        </div>
                        <div>
                            <label class="form-label">E-mail (login) *</label>
                            <input type="email" name="email" x-model="form.email" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Telefone</label>
                            <input type="tel" name="phone" x-model="form.phone" placeholder="(11) 99999-0000" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Comissão (%) *</label>
                            <input type="number" step="0.01" min="0" max="100" name="comissao_percentual" x-model="form.comissao_percentual" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">
                                <span x-text="modo === 'novo' ? 'Senha de acesso *' : 'Nova senha'"></span>
                            </label>
                            <input type="password" name="password" x-model="form.password" :required="modo === 'novo'" autocomplete="new-password" class="form-input">
                            <p class="form-hint mt-1" x-show="modo === 'editar'">Deixe em branco para manter a atual.</p>
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end pt-2">
                        <button type="button" @click="fechar()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">Cancelar</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 rounded-lg" x-text="modo === 'novo' ? 'Adicionar' : 'Salvar'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function colaboradoresPage(config) {
        return {
            aberto: false,
            modo: 'novo',
            storeUrl: '{{ route('configuracoes.colaboradores.store') }}',
            baseUrl: '{{ url('configuracoes/colaboradores') }}',
            form: { id: null, name: '', email: '', phone: '', cargo: '', comissao_percentual: '', password: '' },
            get actionUrl() {
                return this.modo === 'novo' ? this.storeUrl : (this.baseUrl + '/' + this.form.id);
            },
            init() {
                // Reabre o formulário se a validação falhou no servidor.
                if (config.hasErrors) {
                    if (config.old) { this.modo = 'editar'; this.form.id = config.old; this.aberto = true; }
                    else { this.abrirNovo(); }
                }
            },
            abrirNovo() {
                this.modo = 'novo';
                this.form = { id: null, name: '', email: '', phone: '', cargo: '', comissao_percentual: '', password: '' };
                this.aberto = true;
            },
            abrirEdicao(c) {
                this.modo = 'editar';
                this.form = { ...c, password: '' };
                this.aberto = true;
            },
            fechar() { this.aberto = false; },
        };
    }
    </script>
</x-app-layout>
