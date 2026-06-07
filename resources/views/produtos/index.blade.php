<x-app-layout>
    <x-slot name="title">Produtos & Estoque</x-slot>

    <div x-data="produtosPage()">
        {{-- Erros de validação --}}
        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3">
                <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $erro)<li>{{ $erro }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="flex items-center justify-between mb-4 gap-3 flex-wrap">
            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ $busca }}" placeholder="Buscar produto ou código…" class="form-input w-64 max-w-full">
                <button class="px-3 py-2 text-sm rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Buscar</button>
            </form>
            <div class="flex gap-2">
                <a href="{{ route('pdv.index') }}" class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/></svg>
                    Abrir PDV
                </a>
                <button @click="abrirNovo()" class="flex items-center gap-1.5 px-3 py-2 bg-brand text-white text-sm font-medium rounded-lg hover:opacity-90">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Novo produto
                </button>
            </div>
        </div>

        <x-card class="!p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                            <th class="px-4 py-3">Produto</th>
                            <th class="px-4 py-3">Categoria</th>
                            <th class="px-4 py-3 text-right">Preço</th>
                            <th class="px-4 py-3 text-center">Estoque</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/60">
                        @forelse($produtos as $p)
                            @php
                                $pPayload = ['id'=>$p->id,'name'=>$p->name,'sku'=>$p->sku,'category'=>$p->category,'price'=>(float)$p->price,'cost'=>(float)$p->cost,'min_stock'=>$p->min_stock,'unit'=>$p->unit,'active'=>(bool)$p->active];
                            @endphp
                            <tr class="text-gray-700 dark:text-gray-200">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $p->name }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $p->sku ?: '—' }} @unless($p->active)· <span class="text-amber-500">inativo</span>@endunless</p>
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $p->category ?: '—' }}</td>
                                <td class="px-4 py-3 text-right font-medium">R$ {{ number_format($p->price, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span @class([
                                        'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                        'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' => $p->estoque_baixo,
                                        'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' => !$p->estoque_baixo,
                                    ])>
                                        {{ $p->stock_quantity }} {{ $p->unit }}
                                        @if($p->estoque_baixo) · baixo @endif
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-3 justify-end">
                                        <button class="text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-brand" @click="abrirEstoque(@js(['id'=>$p->id,'name'=>$p->name,'stock'=>$p->stock_quantity]))">Estoque</button>
                                        <button class="text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-brand" @click="abrirEdicao(@js($pPayload))">Editar</button>
                                        <x-confirm-delete :action="route('produtos.destroy', $p)" item-name="este produto">
                                            <button type="button" class="text-xs font-medium text-red-500 hover:text-red-700">Excluir</button>
                                        </x-confirm-delete>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">
                                <x-empty-state icon="inbox" title="Nenhum produto" description="Cadastre rações, acessórios e itens para vender no PDV e controlar o estoque." />
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Modal criar/editar produto --}}
        <div x-show="aberto" x-cloak class="fixed inset-0 z-50 flex items-start justify-center pt-10 px-4 pb-4" style="display:none">
            <div x-show="aberto" x-transition.opacity @click="fechar()" class="fixed inset-0 bg-gray-900/50"></div>
            <div x-show="aberto" x-transition class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg z-10 max-h-[90vh] flex flex-col">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100" x-text="modo === 'novo' ? 'Novo produto' : 'Editar produto'"></h3>
                    <button @click="fechar()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <form method="POST" :action="actionUrl" class="p-5 overflow-y-auto space-y-3">
                    @csrf
                    <template x-if="modo === 'editar'"><input type="hidden" name="_method" value="PUT"></template>
                    <div>
                        <label class="form-label">Nome *</label>
                        <input type="text" name="name" x-model="form.name" required class="form-input">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="form-label">Código / SKU</label><input type="text" name="sku" x-model="form.sku" class="form-input"></div>
                        <div><label class="form-label">Categoria</label><input type="text" name="category" x-model="form.category" list="categorias" class="form-input">
                            <datalist id="categorias"><option value="Ração"></option><option value="Acessórios"></option><option value="Higiene"></option><option value="Medicamento"></option><option value="Brinquedo"></option></datalist>
                        </div>
                        <div><label class="form-label">Preço de venda (R$) *</label><input type="number" step="0.01" min="0" name="price" x-model="form.price" required class="form-input"></div>
                        <div><label class="form-label">Custo (R$)</label><input type="number" step="0.01" min="0" name="cost" x-model="form.cost" class="form-input"></div>
                        <div x-show="modo === 'novo'"><label class="form-label">Estoque inicial *</label><input type="number" min="0" name="stock_quantity" x-model="form.stock_quantity" class="form-input"></div>
                        <div><label class="form-label">Estoque mínimo</label><input type="number" min="0" name="min_stock" x-model="form.min_stock" class="form-input"></div>
                        <div><label class="form-label">Unidade</label><input type="text" name="unit" x-model="form.unit" placeholder="un, kg, cx" class="form-input"></div>
                        <div x-show="modo === 'editar'" class="flex items-end">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                <input type="checkbox" name="active" value="1" x-model="form.active" class="rounded border-gray-300 dark:border-gray-600 text-brand focus:ring-brand/20"> Ativo
                            </label>
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end pt-2">
                        <button type="button" @click="fechar()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">Cancelar</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 rounded-lg" x-text="modo === 'novo' ? 'Cadastrar' : 'Salvar'"></button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal ajuste de estoque --}}
        <div x-show="estoqueAberto" x-cloak class="fixed inset-0 z-50 flex items-start justify-center pt-10 px-4 pb-4" style="display:none">
            <div x-show="estoqueAberto" x-transition.opacity @click="estoqueAberto=false" class="fixed inset-0 bg-gray-900/50"></div>
            <div x-show="estoqueAberto" x-transition class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-sm z-10">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Ajustar estoque</h3>
                    <button @click="estoqueAberto=false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <form method="POST" :action="estoqueUrl" class="p-5 space-y-3">
                    @csrf
                    <p class="text-sm text-gray-600 dark:text-gray-300"><span class="font-medium" x-text="estoqueProduto.name"></span> · atual: <span x-text="estoqueProduto.stock"></span></p>
                    <div><label class="form-label">Tipo</label>
                        <select name="type" class="form-select"><option value="entrada">Entrada (compra)</option><option value="saida">Saída (perda/uso)</option><option value="ajuste">Ajuste/inventário</option></select>
                    </div>
                    <div><label class="form-label">Quantidade</label><input type="number" min="1" name="quantity" required class="form-input"></div>
                    <div><label class="form-label">Motivo</label><input type="text" name="reason" class="form-input"></div>
                    <div class="flex gap-3 justify-end pt-1">
                        <button type="button" @click="estoqueAberto=false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 rounded-lg">Cancelar</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 rounded-lg">Aplicar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function produtosPage() {
        return {
            aberto: false, modo: 'novo', estoqueAberto: false,
            storeUrl: '{{ route('produtos.store') }}',
            baseUrl: '{{ url('produtos') }}',
            form: { id:null, name:'', sku:'', category:'', price:'', cost:'', stock_quantity:0, min_stock:0, unit:'un', active:true },
            estoqueProduto: { id:null, name:'', stock:0 },
            get actionUrl() { return this.modo === 'novo' ? this.storeUrl : (this.baseUrl + '/' + this.form.id); },
            get estoqueUrl() { return this.baseUrl + '/' + this.estoqueProduto.id + '/estoque'; },
            init() {
                @if($errors->any()) this.abrirNovo(); @endif
            },
            abrirNovo() { this.modo='novo'; this.form={ id:null, name:'', sku:'', category:'', price:'', cost:'', stock_quantity:0, min_stock:0, unit:'un', active:true }; this.aberto=true; },
            abrirEdicao(p) { this.modo='editar'; this.form={ ...p }; this.aberto=true; },
            fechar() { this.aberto=false; },
            abrirEstoque(p) { this.estoqueProduto = { ...p }; this.estoqueAberto=true; },
        };
    }
    </script>
</x-app-layout>
