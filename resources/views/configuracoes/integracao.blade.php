<x-app-layout>
    <x-slot name="title">Configurações — Integração WhatsApp</x-slot>
    @include('configuracoes._nav')
    <div class="max-w-xl mt-4">
        <x-card title="Integração WhatsApp (Z-API / Evolution API)">
            <form method="POST" action="{{ route('configuracoes.integracao.update') }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="form-label">URL da API</label>
                    <input type="url" name="WHATSAPP_API_URL" value="{{ old('WHATSAPP_API_URL', env('WHATSAPP_API_URL')) }}" class="form-input" placeholder="https://api.z-api.io/...">
                </div>
                <div>
                    <label class="form-label">Token</label>
                    <input type="text" name="WHATSAPP_API_TOKEN" value="{{ old('WHATSAPP_API_TOKEN', env('WHATSAPP_API_TOKEN')) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Instância padrão</label>
                    <input type="text" name="WHATSAPP_DEFAULT_INSTANCE" value="{{ old('WHATSAPP_DEFAULT_INSTANCE', env('WHATSAPP_DEFAULT_INSTANCE')) }}" class="form-input">
                </div>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand hover:opacity-90 rounded-lg">Salvar</button>
            </form>
        </x-card>
    </div>
</x-app-layout>
