<x-app-layout>
    <x-slot name="title">PDV — Frente de Caixa</x-slot>

    <div x-data="pdv(@js($produtos), @js($clientes))" class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Produtos --}}
        <div class="lg:col-span-2">
            <x-card class="!p-4">
                <div class="flex items-center justify-between gap-3 mb-3 flex-wrap">
                    <input type="text" x-model="busca" placeholder="Buscar produto por nome ou código…" class="form-input flex-1 min-w-48">
                    <a href="{{ route('pdv.historico') }}" class="text-xs text-gray-500 dark:text-gray-400 hover:text-brand whitespace-nowrap">Histórico de vendas →</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-[60vh] overflow-y-auto">
                    <template x-for="p in produtosFiltrados" :key="p.id">
                        <button type="button" @click="adicionar(p)" :disabled="p.stock_quantity <= 0"
                                class="text-left p-3 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-brand hover:bg-brand/5 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100 line-clamp-2" x-text="p.name"></p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5" x-text="'Estoque: ' + p.stock_quantity + ' ' + p.unit"></p>
                            <p class="text-sm font-bold text-brand mt-1" x-text="brl(p.price)"></p>
                        </button>
                    </template>
                    <p x-show="produtosFiltrados.length === 0" class="col-span-full text-center text-sm text-gray-400 dark:text-gray-500 py-8">Nenhum produto encontrado.</p>
                </div>
            </x-card>
        </div>

        {{-- Carrinho --}}
        <div>
            <x-card class="!p-0 flex flex-col" style="min-height: 70vh">
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Carrinho</h3>
                    <button x-show="carrinho.length" @click="limpar()" class="text-xs text-red-500 hover:text-red-700">Limpar</button>
                </div>

                <div class="flex-1 overflow-y-auto p-3 space-y-2">
                    <template x-for="item in carrinho" :key="item.id">
                        <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 dark:bg-gray-700/40">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate" x-text="item.name"></p>
                                <p class="text-xs text-gray-400 dark:text-gray-500" x-text="brl(item.price) + ' un'"></p>
                            </div>
                            <div class="flex items-center gap-1">
                                <button @click="mudarQtd(item, -1)" class="w-6 h-6 rounded bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm">−</button>
                                <span class="w-6 text-center text-sm text-gray-800 dark:text-gray-100" x-text="item.qty"></span>
                                <button @click="mudarQtd(item, 1)" class="w-6 h-6 rounded bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm">+</button>
                            </div>
                            <span class="w-20 text-right text-sm font-medium text-gray-800 dark:text-gray-100" x-text="brl(item.price * item.qty)"></span>
                        </div>
                    </template>
                    <p x-show="carrinho.length === 0" class="text-center text-sm text-gray-400 dark:text-gray-500 py-10">Toque nos produtos para adicionar.</p>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700 p-4 space-y-3">
                    <div>
                        <label class="form-label">Cliente (opcional)</label>
                        <select x-model="clienteId" class="form-select">
                            <option value="">Consumidor final</option>
                            <template x-for="c in clientes" :key="c.id"><option :value="c.id" x-text="c.name"></option></template>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Pagamento</label>
                            <select x-model="pagamento" class="form-select">
                                <option value="dinheiro">Dinheiro</option><option value="pix">PIX</option>
                                <option value="debito">Débito</option><option value="credito">Crédito</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Desconto (R$)</label>
                            <input type="number" step="0.01" min="0" x-model.number="desconto" class="form-input">
                        </div>
                    </div>

                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between text-gray-500 dark:text-gray-400"><span>Subtotal</span><span x-text="brl(subtotal)"></span></div>
                        <div class="flex justify-between text-gray-500 dark:text-gray-400"><span>Desconto</span><span x-text="'- ' + brl(desconto || 0)"></span></div>
                        <div class="flex justify-between text-base font-bold text-gray-800 dark:text-gray-100"><span>Total</span><span class="text-brand" x-text="brl(total)"></span></div>
                    </div>

                    <p x-show="erro" x-cloak class="form-error" x-text="erro"></p>

                    <button @click="finalizar()" :disabled="carrinho.length === 0 || salvando"
                            class="w-full py-3 bg-brand text-white text-sm font-semibold rounded-xl hover:opacity-90 disabled:opacity-50 transition-opacity">
                        <span x-show="!salvando">Finalizar venda · <span x-text="brl(total)"></span></span>
                        <span x-show="salvando">Processando…</span>
                    </button>
                </div>
            </x-card>
        </div>
    </div>

    <script>
    function pdv(produtos, clientes) {
        return {
            produtos, clientes,
            busca: '', carrinho: [], clienteId: '', pagamento: 'dinheiro', desconto: 0,
            salvando: false, erro: '',
            brl(v) { return 'R$ ' + (parseFloat(v) || 0).toFixed(2).replace('.', ','); },
            get produtosFiltrados() {
                const t = this.busca.toLowerCase().trim();
                if (!t) return this.produtos;
                return this.produtos.filter(p => p.name.toLowerCase().includes(t) || (p.sku || '').toLowerCase().includes(t));
            },
            get subtotal() { return this.carrinho.reduce((s, i) => s + i.price * i.qty, 0); },
            get total() { return Math.max(0, this.subtotal - (parseFloat(this.desconto) || 0)); },
            adicionar(p) {
                const ex = this.carrinho.find(i => i.id === p.id);
                if (ex) { this.mudarQtd(ex, 1); return; }
                if (p.stock_quantity <= 0) return;
                this.carrinho.push({ id: p.id, name: p.name, price: parseFloat(p.price), qty: 1, max: p.stock_quantity });
            },
            mudarQtd(item, delta) {
                item.qty += delta;
                if (item.qty <= 0) { this.carrinho = this.carrinho.filter(i => i.id !== item.id); return; }
                if (item.qty > item.max) { item.qty = item.max; this.erro = 'Estoque máximo de "' + item.name + '" atingido.'; setTimeout(() => this.erro = '', 2500); }
            },
            limpar() { this.carrinho = []; this.desconto = 0; this.erro = ''; },
            async finalizar() {
                if (this.salvando || this.carrinho.length === 0) return;
                this.salvando = true; this.erro = '';
                try {
                    const r = await fetch('{{ route('pdv.store') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify({
                            cliente_id: this.clienteId || null,
                            payment_method: this.pagamento,
                            desconto: parseFloat(this.desconto) || 0,
                            itens: this.carrinho.map(i => ({ produto_id: i.id, quantity: i.qty })),
                        }),
                    });
                    const j = await r.json().catch(() => ({}));
                    if (r.ok && j.success) {
                        sessionStorage.setItem('flash', JSON.stringify({ type: 'success', text: 'Venda ' + j.venda.id + ' registrada · ' + j.venda.total }));
                        window.location.reload();
                        return;
                    }
                    this.erro = (r.status === 422 && j.errors) ? Object.values(j.errors).flat().join(' ') : (j.message || 'Não foi possível concluir a venda.');
                } catch (e) { this.erro = 'Erro de conexão. Tente novamente.'; }
                finally { this.salvando = false; }
            },
        };
    }
    </script>
</x-app-layout>
