<x-app-layout>
    <x-slot name="title">Configurações — Petshop</x-slot>
    @include('configuracoes._nav')
    <div class="max-w-xl mt-4">
        <x-card title="Dados do Petshop">
            <form method="POST" action="{{ route('configuracoes.petshop.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nome *</label>
                    <input type="text" name="name" value="{{ old('name', $petshop->name) }}" required class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Telefone</label>
                        <input type="text" name="phone" value="{{ old('phone', $petshop->phone) }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Cor de destaque</label>
                        <div class="flex gap-2">
                            <input type="color" name="primary_color" value="{{ old('primary_color', $petshop->primary_color) }}" class="w-10 h-10 border border-gray-300 rounded cursor-pointer">
                            <input type="text" name="primary_color" value="{{ old('primary_color', $petshop->primary_color) }}" class="flex-1 text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Endereço</label>
                    <input type="text" name="address" value="{{ old('address', $petshop->address) }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Logo</label>
                    @if($petshop->logo) <img src="{{ Storage::url($petshop->logo) }}" class="h-16 mb-2 rounded-lg"> @endif
                    <input type="file" name="logo" accept="image/*" class="text-sm text-gray-600">
                </div>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 rounded-lg">Salvar</button>
            </form>
        </x-card>
    </div>
</x-app-layout>
