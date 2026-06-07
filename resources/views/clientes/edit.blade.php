<x-app-layout>
    <x-slot name="title">Editar Cliente</x-slot>
    <div class="max-w-xl">
        <a href="{{ route('clientes.show', $cliente) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-brand mb-4 block">← {{ $cliente->name }}</a>
        <x-card title="Editar cliente">
            <form method="POST" action="{{ route('clientes.update', $cliente) }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="form-label">Nome *</label>
                    <input type="text" name="name" value="{{ old('name', $cliente->name) }}" required class="form-input">
                </div>
                <div>
                    <label class="form-label">Telefone *</label>
                    <input type="text" name="phone" value="{{ old('phone', $cliente->phone) }}" required class="form-input">
                </div>
                <div>
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" value="{{ old('email', $cliente->email) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Data de nascimento</label>
                    <input type="date" name="birthdate" value="{{ old('birthdate', $cliente->birthdate?->format('Y-m-d')) }}" class="form-input">
                </div>
                <div class="flex gap-3 justify-end pt-2">
                    <a href="{{ route('clientes.show', $cliente) }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">Cancelar</a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 rounded-lg">Salvar</button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
