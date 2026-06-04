<x-app-layout>
    <x-slot name="title">Novo Cliente</x-slot>
    <div class="max-w-xl">
        <a href="{{ route('clientes.index') }}" class="text-sm text-gray-500 hover:text-brand mb-4 block">← Clientes</a>
        <x-card title="Cadastrar cliente">
            <form method="POST" action="{{ route('clientes.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nome *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Telefone *</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                    @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Data de nascimento</label>
                    <input type="date" name="birthdate" value="{{ old('birthdate') }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                </div>
                <div class="flex gap-3 justify-end pt-2">
                    <a href="{{ route('clientes.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">Cancelar</a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 rounded-lg">Salvar</button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
