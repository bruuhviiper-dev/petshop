<x-app-layout>
    <x-slot name="title">Novo Pet</x-slot>
    <div class="max-w-xl">
        <a href="{{ route('clientes.show', $cliente) }}" class="text-sm text-gray-500 hover:text-brand mb-4 block">← {{ $cliente->name }}</a>
        <x-card title="Cadastrar pet">
            <form method="POST" action="{{ route('clientes.pets.store', $cliente) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nome *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Espécie *</label>
                        <select name="species" required class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                            <option value="">Selecione</option>
                            <option value="Cachorro" {{ old('species') === 'Cachorro' ? 'selected' : '' }}>Cachorro</option>
                            <option value="Gato" {{ old('species') === 'Gato' ? 'selected' : '' }}>Gato</option>
                            <option value="Ave" {{ old('species') === 'Ave' ? 'selected' : '' }}>Ave</option>
                            <option value="Outro" {{ old('species') === 'Outro' ? 'selected' : '' }}>Outro</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Raça</label>
                        <input type="text" name="breed" value="{{ old('breed') }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Peso (kg)</label>
                        <input type="number" step="0.1" name="weight" value="{{ old('weight') }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Temperamento</label>
                    <input type="text" name="temperament" value="{{ old('temperament') }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Alergias</label>
                    <textarea name="allergies" rows="2" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand resize-none">{{ old('allergies') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Foto</label>
                    <input type="file" name="photo" accept="image/*" class="w-full text-sm text-gray-600">
                </div>
                <div class="flex gap-3 justify-end pt-2">
                    <a href="{{ route('clientes.show', $cliente) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">Cancelar</a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 rounded-lg">Salvar</button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
