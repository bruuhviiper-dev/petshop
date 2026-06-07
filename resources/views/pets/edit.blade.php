<x-app-layout>
    <x-slot name="title">Editar {{ $pet->name }}</x-slot>
    <div class="max-w-xl">
        <a href="{{ route('clientes.show', $cliente) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-brand mb-4 block">← {{ $cliente->name }}</a>
        <x-card title="Editar pet">
            <form method="POST" action="{{ route('clientes.pets.update', [$cliente, $pet]) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nome *</label>
                        <input type="text" name="name" value="{{ old('name', $pet->name) }}" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Espécie *</label>
                        <select name="species" required class="form-input">
                            @foreach(['Cachorro','Gato','Ave','Outro'] as $esp)
                                <option value="{{ $esp }}" {{ old('species', $pet->species) === $esp ? 'selected' : '' }}>{{ $esp }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Raça</label>
                        <input type="text" name="breed" value="{{ old('breed', $pet->breed) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Peso (kg)</label>
                        <input type="number" step="0.1" name="weight" value="{{ old('weight', $pet->weight) }}" class="form-input">
                    </div>
                </div>
                <div>
                    <label class="form-label">Alergias</label>
                    <textarea name="allergies" rows="2" class="form-textarea resize-none">{{ old('allergies', $pet->allergies) }}</textarea>
                </div>
                <div>
                    <label class="form-label">Nova foto</label>
                    @if($pet->photo) <img src="{{ Storage::url($pet->photo) }}" class="h-20 rounded-lg mb-2 object-cover"> @endif
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
