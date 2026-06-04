<x-app-layout>
    <x-slot name="title">Configurações — Integração WhatsApp</x-slot>
    @include('configuracoes._nav')
    <div class="max-w-xl mt-4">
        <x-card title="Integração WhatsApp (Z-API / Evolution API)">
            <form method="POST" action="{{ route('configuracoes.integracao.update') }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">URL da API</label>
                    <input type="url" name="WHATSAPP_API_URL" value="{{ old('WHATSAPP_API_URL', env('WHATSAPP_API_URL')) }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand" placeholder="https://api.z-api.io/...">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Token</label>
                    <input type="text" name="WHATSAPP_API_TOKEN" value="{{ old('WHATSAPP_API_TOKEN', env('WHATSAPP_API_TOKEN')) }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Instância padrão</label>
                    <input type="text" name="WHATSAPP_DEFAULT_INSTANCE" value="{{ old('WHATSAPP_DEFAULT_INSTANCE', env('WHATSAPP_DEFAULT_INSTANCE')) }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/20 focus:border-brand">
                </div>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 rounded-lg">Salvar</button>
            </form>
        </x-card>
    </div>
</x-app-layout>
