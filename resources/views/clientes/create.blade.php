<x-app-layout>
    <x-slot name="title">Novo Cliente</x-slot>
    <div class="max-w-xl">
        <a href="{{ route('clientes.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-brand mb-4 block">← Clientes</a>
        <x-card title="Cadastrar cliente">
            <form method="POST" action="{{ route('clientes.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label">Nome *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-input">
                    @error('name') <p class="form-error mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Telefone *</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required class="form-input">
                    @error('phone') <p class="form-error mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Data de nascimento</label>
                    <input type="date" name="birthdate" value="{{ old('birthdate') }}" class="form-input">
                </div>
                <div class="flex gap-3 justify-end pt-2">
                    <a href="{{ route('clientes.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">Cancelar</a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 rounded-lg">Salvar</button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
