<x-app-layout>
    <x-slot name="title">Clientes</x-slot>

    <div x-data="{ busca: '{{ request('q') }}' }">
        <div class="flex items-center justify-between mb-4">
            <form method="GET" action="{{ route('clientes.index') }}" class="flex gap-2">
                <input type="text" name="q" x-model="busca" placeholder="Buscar por nome ou telefone..."
                       class="text-sm border border-gray-300 rounded-lg px-3 py-2 w-64 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                <button type="submit" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-sm rounded-lg text-gray-700">Buscar</button>
            </form>
            <a href="{{ route('clientes.create') }}"
               class="flex items-center gap-2 px-4 py-2 bg-brand text-white text-sm font-medium rounded-lg hover:opacity-90">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Novo cliente
            </a>
        </div>

        <x-card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 uppercase tracking-wide border-b border-gray-100">
                            <th class="pb-3 pr-4">Nome</th>
                            <th class="pb-3 pr-4">Telefone</th>
                            <th class="pb-3 pr-4">E-mail</th>
                            <th class="pb-3 pr-4 text-center">Pets</th>
                            <th class="pb-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($clientes as $cliente)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 pr-4">
                                    <div class="flex items-center gap-2">
                                        <x-avatar :name="$cliente->name" />
                                        <span class="font-medium text-gray-800">{{ $cliente->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 pr-4 text-gray-600">{{ $cliente->phone }}</td>
                                <td class="py-3 pr-4 text-gray-500">{{ $cliente->email ?? '—' }}</td>
                                <td class="py-3 pr-4 text-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-brand/10 text-brand text-xs font-bold rounded-full">
                                        {{ $cliente->pets_count }}
                                    </span>
                                </td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('clientes.show', $cliente) }}" class="text-xs text-brand hover:underline">Ver ficha</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-8 text-center text-sm text-gray-400">Nenhum cliente encontrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($clientes->hasPages())
                <div class="mt-4 pt-4 border-t border-gray-100">{{ $clientes->links() }}</div>
            @endif
        </x-card>
    </div>
</x-app-layout>
