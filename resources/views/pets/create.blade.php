<x-app-layout>
    <x-slot name="title">Novo Pet</x-slot>
    <div class="max-w-xl">
        <a href="{{ route('clientes.show', $cliente) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-brand mb-4 block">← {{ $cliente->name }}</a>
        <x-card title="Cadastrar pet">
            <form method="POST" action="{{ route('clientes.pets.store', $cliente) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nome *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Espécie *</label>
                        <select name="species" required class="form-input">
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
                        <label class="form-label">Raça</label>
                        <input type="text" name="breed" value="{{ old('breed') }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Peso (kg)</label>
                        <input type="number" step="0.1" name="weight" value="{{ old('weight') }}" class="form-input">
                    </div>
                </div>
                <div>
                    <label class="form-label">Temperamento</label>
                    <input type="text" name="temperament" value="{{ old('temperament') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Alergias</label>
                    <textarea name="allergies" rows="2" class="form-textarea resize-none">{{ old('allergies') }}</textarea>
                </div>
                <div>
                    <label class="form-label">Foto</label>
                    <input type="file" name="photo" accept="image/*" class="w-full text-sm text-gray-600 dark:text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-brand-soft file:text-brand hover:file:opacity-80 file:cursor-pointer">
                </div>
                <div class="flex gap-3 justify-end pt-2">
                    <a href="{{ route('clientes.show', $cliente) }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">Cancelar</a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 rounded-lg">Salvar</button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
