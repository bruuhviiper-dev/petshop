<x-app-layout>
    <x-slot name="title">Configurações — Petshop</x-slot>
    @include('configuracoes._nav')
    <div class="max-w-xl mt-4">
        <x-card title="Dados do Petshop">
            <form method="POST" action="{{ route('configuracoes.petshop.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Nome *</label>
                    <input type="text" name="name" value="{{ old('name', $petshop->name) }}" required class="w-full text-sm border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone</label>
                        <input type="text" name="phone" value="{{ old('phone', $petshop->phone) }}" class="w-full text-sm border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                    </div>

                    {{-- Cor de destaque: seletor + texto sincronizados via Alpine.
                         Apenas UM input envia o valor (o de texto), evitando conflito de name duplicado. --}}
                    <div x-data="{ color: '{{ old('primary_color', $petshop->primary_color) }}' }">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Cor de destaque</label>
                        <div class="flex gap-2 items-center">
                            <input type="color" x-model="color" aria-label="Selecionar cor"
                                   class="w-10 h-10 p-0.5 border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer bg-white dark:bg-gray-800 shrink-0">
                            <input type="text" name="primary_color" x-model="color" maxlength="7"
                                   class="flex-1 min-w-0 text-sm font-mono uppercase border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                        </div>
                        <x-input-error :messages="$errors->get('primary_color')" class="mt-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Endereço</label>
                    <input type="text" name="address" value="{{ old('address', $petshop->address) }}" class="w-full text-sm border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                    <x-input-error :messages="$errors->get('address')" class="mt-1" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Link público de agendamento</label>
                    <div class="flex items-center gap-1">
                        <span class="text-xs text-gray-400 dark:text-gray-500 shrink-0">{{ url('/agendar') }}/</span>
                        <input type="text" name="slug" value="{{ old('slug', $petshop->slug) }}"
                               class="flex-1 min-w-0 text-sm font-mono border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                    </div>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">⚠️ Mudar o link invalida QR Codes e links já compartilhados. O nome do petshop pode ser alterado sem afetar o link.</p>
                    <x-input-error :messages="$errors->get('slug')" class="mt-1" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Logo</label>
                    @if($petshop->logo) <img src="{{ Storage::url($petshop->logo) }}" class="h-16 mb-2 rounded-lg"> @endif
                    <input type="file" name="logo" accept="image/*" class="text-sm text-gray-600 dark:text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-brand-soft file:text-brand hover:file:opacity-80 file:cursor-pointer">
                    <x-input-error :messages="$errors->get('logo')" class="mt-1" />
                </div>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white btn-brand rounded-lg shadow-sm">Salvar alterações</button>
            </form>
        </x-card>
    </div>
</x-app-layout>
